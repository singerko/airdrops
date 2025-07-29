<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\User;
use App\Models\Airdrop;
use App\Models\Notification;
use App\Mail\AirdropNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendAirdropUpdate(Airdrop $airdrop, string $type, array $data = []): void
    {
        $subscribers = $airdrop->subscriptions()
            ->with('user')
            ->where('email_notifications', true)
            ->get();

        foreach ($subscribers as $subscription) {
            $user = $subscription->user;
            
            // Check if user wants this type of notification
            $notificationTypes = $subscription->notification_types ?? [];
            if (!in_array($type, $notificationTypes)) {
                continue;
            }

            // Create in-app notification
            $this->createNotification($user, $airdrop, $type, $data);

            // Send email notification
            if ($this->shouldSendEmail($user, $type)) {
                $this->sendEmailNotification($user, $airdrop, $type, $data);
            }
        }
    }

    private function createNotification(User $user, Airdrop $airdrop, string $type, array $data): void
    {
        $notification = new Notification([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $this->getNotificationTitle($type, $airdrop),
            'message' => $this->getNotificationMessage($type, $airdrop, $data),
            'data' => array_merge($data, [
                'airdrop_id' => $airdrop->id,
                'airdrop_slug' => $airdrop->slug,
            ]),
        ]);

        $notification->save();
    }

    private function sendEmailNotification(User $user, Airdrop $airdrop, string $type, array $data): void
    {
        try {
            Mail::to($user->email)->queue(new AirdropNotification($user, $airdrop, $type, $data));
        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'user_id' => $user->id,
                'airdrop_id' => $airdrop->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function shouldSendEmail(User $user, string $type): bool
    {
        $settings = $user->notification_settings ?? [];
        
        $emailEnabled = $settings['email_notifications'] ?? true;
        
        if (!$emailEnabled) {
            return false;
        }

        // Check specific notification type settings
        switch ($type) {
            case 'new_phase':
                return $settings['airdrop_updates'] ?? true;
            case 'deadline_reminder':
                return $settings['deadline_reminders'] ?? true;
            case 'status_change':
                return $settings['airdrop_updates'] ?? true;
            default:
                return true;
        }
    }

    private function getNotificationTitle(string $type, Airdrop $airdrop): string
    {
        return match($type) {
            'new_phase' => "New Phase: {$airdrop->title}",
            'deadline_reminder' => "Deadline Reminder: {$airdrop->title}",
            'status_change' => "Status Update: {$airdrop->title}",
            'airdrop_ending' => "Ending Soon: {$airdrop->title}",
            default => "Update: {$airdrop->title}",
        };
    }

    private function getNotificationMessage(string $type, Airdrop $airdrop, array $data): string
    {
        return match($type) {
            'new_phase' => "A new phase has been added to {$airdrop->title}. Check it out!",
            'deadline_reminder' => "{$airdrop->title} is ending soon. Don't miss out!",
            'status_change' => "The status of {$airdrop->title} has been updated to {$airdrop->status}.",
            'airdrop_ending' => "{$airdrop->title} is ending in " . ($data['days'] ?? 'few') . " days.",
            default => "There's an update for {$airdrop->title}.",
        };
    }

    public function sendWeeklyDigest(User $user): void
    {
        // Get user's preferred blockchains
        $preferredBlockchains = $user->preferred_blockchains ?? [];
        
        $query = Airdrop::published()
            ->with(['project', 'blockchain'])
            ->where('published_at', '>=', now()->subWeek());

        if (!empty($preferredBlockchains)) {
            $query->whereIn('blockchain_id', $preferredBlockchains);
        }

        $newAirdrops = $query->latest('published_at')->limit(10)->get();

        if ($newAirdrops->isNotEmpty()) {
            try {
                Mail::to($user->email)->queue(new \App\Mail\WeeklyDigest($user, $newAirdrops));
            } catch (\Exception $e) {
                Log::error('Failed to send weekly digest', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
