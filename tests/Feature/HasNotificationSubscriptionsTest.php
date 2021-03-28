<?php

namespace LiranCo\NotificationSubscriptions\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use LiranCo\NotificationSubscriptions\Models\NotificationSubscription;
use LiranCo\NotificationSubscriptions\Tests\TestCase;
use LiranCo\NotificationSubscriptions\Tests\TestModels\User;

class HasNotificationSubscriptionsTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function can_subscribe_to_notification()
    {
        //Arrange
        /** @var User $user */
        $user = User::factory()->create();

        $this->assertEquals(0, NotificationSubscription::count());

        //Act
        $type = $this->faker->word;
        $user->subscribe($type);

        //Assert
        $user->refresh();
        $this->assertEquals(1, $user->notificationSubscriptions->count());

        $this->assertEquals($type, $user->notificationSubscriptions->first()->type);
        $this->assertEquals('*', $user->notificationSubscriptions->first()->channel);
    }

    /** @test */
    public function can_subscribe_to_notification_specific_channel()
    {
        //Arrange
        /** @var User $user */
        $user = User::factory()->create();

        //Act
        $user->subscribe('notification', 'sms');

        //Assert
        $user->refresh();
        $this->assertEquals(1, $user->notificationSubscriptions->count());

        $this->assertEquals('notification', $user->notificationSubscriptions->first()->type);
        $this->assertEquals('sms', $user->notificationSubscriptions->first()->channel);
    }

    /** @test */
    public function can_unsubscribe_from_notification()
    {
        //Arrange
        /** @var User $user */
        $user = User::factory()->create();

        $type = $this->faker->word;
        $user->subscribe($type);

        $this->assertNull(NotificationSubscription::first()->unsubscribed_at);

        //Act
        $user->unsubscribe($type);

        //Assert
        $this->assertNotNull(NotificationSubscription::first()->unsubscribed_at);
    }

    /** @test */
    public function can_unsubscribe_from_notification_specific_channel()
    {
        //Arrange
        /** @var User $user */
        $user = User::factory()->create();

        $smsNotification = $user->subscribe('notification', 'sms');
        $emailNotification = $user->subscribe('notification', 'email');

        $this->assertNull($smsNotification->unsubscribed_at);
        $this->assertNull($emailNotification->unsubscribed_at);

        //Act
        $user->unsubscribe('notification', 'sms');

        //Assert
        $smsNotification->refresh();
        $emailNotification->refresh();

        $this->assertNotNull($smsNotification->unsubscribed_at);
        $this->assertNull($emailNotification->unsubscribed_at);
    }
}
