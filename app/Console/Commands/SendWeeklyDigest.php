<?php
// app/Console/Commands/SendWeeklyDigest.php

namespace App\Console\Commands;

use App\Jobs\SendWeeklyDigest;
use Illuminate\Console\Command;

class SendWeeklyDigestCommand extends Command
{
    protected $signature = 'notifications:weekly-digest';
    protected $description = 'Send weekly digest emails to subscribed users';

    public function handle()
    {
        $this->info('Sending weekly digest emails...');
        
        SendWeeklyDigest::dispatch();
        
        $this->info('Weekly digest emails queued successfully.');
    }
}
