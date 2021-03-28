<?php


namespace LiranCo\NotificationSubscriptions\Tests\TestNotification;


use Illuminate\Notifications\Notification;

class InvoicePaidNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail', 'sms'];
    }
}