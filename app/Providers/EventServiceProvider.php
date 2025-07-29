<?php
// app/Providers/EventServiceProvider.php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\AirdropCreated;
use App\Events\AirdropUpdated;
use App\Events\AirdropStatusChanged;
use App\Events\NewPhaseAdded;
use App\Listeners\SendAirdropNotifications;
use App\Listeners\UpdateAirdropStatus;
use App\Listeners\SendNewPhaseNotifications;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        AirdropCreated::class => [
            SendAirdropNotifications::class,
        ],

        AirdropUpdated::class => [
            SendAirdropNotifications::class,
        ],

        AirdropStatusChanged::class => [
            SendAirdropNotifications::class,
        ],

        NewPhaseAdded::class => [
            SendNewPhaseNotifications::class,
        ],
    ];

    public function boot()
    {
        //
    }

    public function shouldDiscoverEvents()
    {
        return false;
    }
}
