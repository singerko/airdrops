<?php
// app/Console/Commands/UpdateAirdropStatuses.php

namespace App\Console\Commands;

use App\Jobs\UpdateAirdropStatuses;
use Illuminate\Console\Command;

class UpdateAirdropStatusesCommand extends Command
{
    protected $signature = 'airdrops:update-statuses';
    protected $description = 'Update airdrop statuses based on start and end dates';

    public function handle()
    {
        $this->info('Updating airdrop statuses...');
        
        UpdateAirdropStatuses::dispatch();
        
        $this->info('Airdrop statuses updated successfully.');
    }
}
