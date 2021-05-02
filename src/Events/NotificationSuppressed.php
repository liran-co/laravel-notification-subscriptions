<?php

namespace LiranCo\NotificationSubscriptions\Events;

use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Queue\SerializesModels;

class NotificationSuppressed
{
    use SerializesModels;

    public $event;
    
    public $notification;
    
    public $notifiable;
    
    public $channel;
    
    public function __construct(NotificationSending $event)
    {
        $this->event = $event;
        
        $this->notification = $event->notification;
        
        $this->notifiable = $event->notifiable;
        
        $this->channel = $event->channel;
    }
}