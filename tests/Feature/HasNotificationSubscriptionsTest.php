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
        $this->assertEquals(1, NotificationSubscription::count());
    }

    /** @test */
    public function can_unsubscribe_to_notification()
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
}