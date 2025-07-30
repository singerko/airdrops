<?php
// app/Console/Commands/CleanupExpiredData.php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CleanupExpiredData extends Command
{
    protected $signature = 'cleanup:expired-data';
    protected $description = 'Clean up expired notifications and cache data';

    public function handle()
    {
        $this->info('Cleaning up expired data...');
        
        // Delete old notifications (older than 30 days)
        $deletedNotifications = Notification::where('created_at', '<', now()->subDays(30))->delete();
        $this->info("Deleted {$deletedNotifications} old notifications.");
        
        // Clear expired cache
        Cache::flush();
        $this->info('Cache cleared.');
        
        // Clean up wallet nonces
        $this->cleanupWalletNonces();
        
        $this->info('Cleanup completed successfully.');
    }

    private function cleanupWalletNonces()
    {
        $keys = Cache::get('wallet_nonce_keys', []);
        $cleaned = 0;
        
        foreach ($keys as $key) {
            if (!Cache::has($key)) {
                $cleaned++;
            }
        }
        
        $this->info("Cleaned up {$cleaned} expired wallet nonces.");
    }
}
