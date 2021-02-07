<?php

namespace LiranCo\NotificationSubscriptions\Tests;

use CreateNotificationSubscriptionsTable;
use CreateUsersTable;
use LiranCo\NotificationSubscriptions\NotificationSubscriptionsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        //
    }

    protected function getPackageProviders($app)
    {
        return [
            NotificationSubscriptionsServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        include_once __DIR__ . '/../database/migrations/2020_05_20_000001_create_notification_subscriptions_table.php';

        include_once __DIR__ . '/database/migrations/create_users_table.php';

        (new CreateNotificationSubscriptionsTable)->up();
        (new CreateUsersTable())->up();
    }
}
