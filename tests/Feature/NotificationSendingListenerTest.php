<?php

namespace LiranCo\NotificationSubscriptions\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\Events\NotificationSending;
use LiranCo\NotificationSubscriptions\Listeners\NotificationSendingListener;
use LiranCo\NotificationSubscriptions\Tests\TestCase;
use LiranCo\NotificationSubscriptions\Tests\TestModels\User;
use LiranCo\NotificationSubscriptions\Tests\TestNotification\InvoicePaidNotification;

class NotificationSendingListenerTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function should_send_notification()
    {
        //Arrange
        /** @var User $user */
        $user = User::factory()->create();

        $user->subscribe(InvoicePaidNotification::class);

        //Act
        $event = new NotificationSending($user, new InvoicePaidNotification(), null);

        $result = (new NotificationSendingListener())->handle($event);

        //Assert
        $this->assertNotFalse($result);
    }

    /** @test */
    public function should_not_send_notification_if_unsubscribed()
    {
        //Arrange
        /** @var User $user */
        $user = User::factory()->create();

        $user->unsubscribe(InvoicePaidNotification::class);

        //Act
        $event = new NotificationSending($user, new InvoicePaidNotification(), null);

        $result = (new NotificationSendingListener())->handle($event);

        //Assert
        $this->assertFalse($result);
    }

    /** @test */
    public function should_not_send_notification_if_unsubscribed_to_channel()
    {
        //Arrange
        /** @var User $user */
        $user = User::factory()->create();

        $user->subscribe(InvoicePaidNotification::class, 'mail');

        $user->unsubscribe(InvoicePaidNotification::class, 'sms');

        //Act
        $mailResult = (new NotificationSendingListener())
            ->handle(new NotificationSending($user, new InvoicePaidNotification(), 'mail'));

        $smsResult = (new NotificationSendingListener())
            ->handle(new NotificationSending($user, new InvoicePaidNotification(), 'sms'));

        //Assert
        $this->assertNotFalse($mailResult);
        $this->assertFalse($smsResult);
    }
}
