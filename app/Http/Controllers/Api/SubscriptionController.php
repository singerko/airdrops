<?php
// app/Http/Controllers/Api/SubscriptionController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Airdrop;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions = auth()->user()
            ->subscriptions()
            ->with(['airdrop.project', 'airdrop.blockchain'])
            ->paginate($request->per_page ?? 12);

        return response()->json([
            'data' => $subscriptions->items(),
            'meta' => [
                'current_page' => $subscriptions->currentPage(),
                'per_page' => $subscriptions->perPage(),
                'total' => $subscriptions->total(),
                'last_page' => $subscriptions->lastPage(),
            ],
        ]);
    }

    public function subscribe(Airdrop $airdrop)
    {
        $user = auth()->user();
        
        $subscription = $user->subscriptions()->firstOrCreate(
            ['airdrop_id' => $airdrop->id],
            [
                'email_notifications' => true,
                'push_notifications' => false,
                'notification_types' => ['new_phase', 'deadline_reminder', 'status_change'],
            ]
        );

        return response()->json([
            'success' => true,
            'subscribed' => true,
            'message' => 'Successfully subscribed to notifications.',
        ]);
    }

    public function unsubscribe(Airdrop $airdrop)
    {
        $user = auth()->user();
        
        $user->subscriptions()
            ->where('airdrop_id', $airdrop->id)
            ->delete();

        return response()->json([
            'success' => true,
            'subscribed' => false,
            'message' => 'Unsubscribed from notifications.',
        ]);
    }
}
