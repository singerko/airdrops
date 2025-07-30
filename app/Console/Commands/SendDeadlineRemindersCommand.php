<?php
// app/Console/Commands/SendDeadlineReminders.php

namespace App\Console\Commands;

use App\Jobs\SendDeadlineReminders;
use Illuminate\Console\Command;

class SendDeadlineRemindersCommand extends Command
{
    protected $signature = 'notifications:deadline-reminders';
    protected $description = 'Send deadline reminder notifications';

    public function handle()
    {
        $this->info('Sending deadline reminders...');
        
        SendDeadlineReminders::dispatch();
        
        $this->info('Deadline reminders sent successfully.');
    }
}
