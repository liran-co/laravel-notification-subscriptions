<?php

namespace LiranCo\NotificationSubscriptions\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Queue\SerializesModels;

class NotificationSuppressedEvent
{
    use Dispatchable;
    use SerializesModels;

    private $event;

    private $notification;

    private $channel;

    private $model;

    public function __construct(NotificationSending $event, $notification, string $channel, ?string $model)
    {
        $this->event = $event;
        $this->notification = $notification;
        $this->channel = $channel;
        $this->model = $model;
    }

    public function getSuppressedEvent()
    {
        return $this->event;
    }

    public function getNotification()
    {
        return $this->notification;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function getModel()
    {
        return $this->model;
    }
}
