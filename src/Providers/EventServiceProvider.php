<?php

namespace LiranCo\NotificationSubscriptions\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'Illuminate\Notifications\Events\NotificationSending' => [
            'LiranCo\NotificationSubscriptions\Listeners\NotificationSendingListener',
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
