<?php
// app/Console/Commands/CleanupExpiredData.php (upravený bez Redis)

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\WalletNonce;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredDataCommand extends Command
{
    protected $signature = 'cleanup:expired-data';
    protected $description = 'Clean up expired notifications, nonces and cache data';

    public function handle()
    {
        $this->info('Cleaning up expired data...');
        
        // Delete old notifications (older than 30 days)
        $deletedNotifications = Notification::where('created_at', '<', now()->subDays(30))->delete();
        $this->info("Deleted {$deletedNotifications} old notifications.");
        
        // Delete expired wallet nonces
        $deletedNonces = WalletNonce::expired()->delete();
        $this->info("Deleted {$deletedNonces} expired wallet nonces.");
        
        // Clean up old cache entries (if using database cache)
        if (config('cache.default') === 'database') {
            $deletedCache = DB::table('cache')->where('expiration', '<', now()->timestamp)->delete();
            $this->info("Deleted {$deletedCache} expired cache entries.");
        }
        
        // Clean up file cache (if using file cache)
        if (config('cache.default') === 'file') {
            $this->cleanupFileCache();
        }
        
        // Clean up old failed jobs (older than 7 days)
        $deletedFailedJobs = DB::table('failed_jobs')->where('failed_at', '<', now()->subDays(7))->delete();
        $this->info("Deleted {$deletedFailedJobs} old failed jobs.");
        
        // Clean up old sessions (older than session lifetime + 1 day)
        $sessionLifetime = config('session.lifetime') + 1440; // +1 day in minutes
        $deletedSessions = DB::table('sessions')
            ->where('last_activity', '<', now()->subMinutes($sessionLifetime)->timestamp)
            ->delete();
        $this->info("Deleted {$deletedSessions} old sessions.");
        
        $this->info('Cleanup completed successfully.');
    }

    private function cleanupFileCache()
    {
        $cachePath = storage_path('framework/cache/data');
        
        if (!is_dir($cachePath)) {
            return;
        }
        
        $files = glob($cachePath . '/*');
        $deletedFiles = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                // Check if file is older than 1 day
                if (filemtime($file) < time() - 86400) {
                    unlink($file);
                    $deletedFiles++;
                }
            }
        }
        
        $this->info("Deleted {$deletedFiles} old cache files.");
    }
}
