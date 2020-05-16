# Laravel Notification Subscriptions

Laravel Notification Subscriptions is a package that hooks directly into Laravel's existing [notification system](https://laravel.com/docs/master/notifications) and adds functionality to manage user subscriptions to your app's notifications and suppress them automatically when they shouldn't be sent. You can subscribe and unsubscribe users to specific notification channels, create opt-in notifications, and scope your subscriptions by another model.

[![Latest Stable Version](https://poser.pugx.org/liran-co/laravel-notification-subscriptions/v/stable)](https://packagist.org/packages/liran-co/laravel-notification-subscriptions) [![Total Downloads](https://poser.pugx.org/liran-co/laravel-notification-subscriptions/downloads)](https://packagist.org/packages/liran-co/laravel-notification-subscriptions) [![License](https://poser.pugx.org/liran-co/laravel-notification-subscriptions/license)](https://packagist.org/packages/liran-co/laravel-notification-subscriptions)

## Installation

To get started, install the `liran-co/laravel-notification-subscriptions` package:

```bash
composer require liran-co/laravel-notification-subscriptions
```

Run the migration to create the `notification_subscriptions` table:
```bash
php artisan migrate
```

Optionally publish the configuration file by running and selecting the appropriate provider option:
```bash
php artisan vendor:publish
```

## Basic usage

This package uses a [Listener](https://laravel.com/docs/master/events) to listen for any notifications that get sent in your application. When a notification gets triggered, the package checks to see if the notification should actually be sent according to the user's subscriptions. If not, the notification is suppressed.

### Getting started

This package assumes you've already setup Laravel's notification system. If you haven't [read the docs](https://laravel.com/docs/master/notifications) to get started.

Add the `HasNotificationSubscriptions ` trait to your `User` model:

```php
use Illuminate\Database\Eloquent\Model;
use LiranCo\NotificationSubscriptions\Traits\HasNotificationSubscriptions;

class User extends Model
{
    use HasNotificationSubscriptions;

    // ...
}
```

### Unsubscribing

To unsubscribe a user from a specific `Notification`, pass the class name of that notification to the `unsubscribe` function.

```php
use App\Notifications\InvoicePaid;

$user->unsubscribe(InvoicePaid::class); //You can also pass a string, but this is the preferred method.
```

The above will unsubscribe the user from all channels. You can unsubscribe a user from a specific channel by passing the channel name as the second parameter:

```php
use App\Notifications\InvoicePaid;

$user->unsubscribe(InvoicePaid::class, 'mail');
```

Now, whenever an `InvoicePaid` notification is sent, the package will automatically detect that the user has unsubscribed and suppress the notification. For example:

```php
use App\Notifications\InvoicePaid;

$user->notify(new InvoicePaid($invoice)); //This won't get sent.
```

### Opt-in notifications

By default, all notifications will be sent if no subscribe/unsubscribe record is found. This means you don't need to explicitly **subscribe** a user to a notification, you only need to **unsubscribe** them.

In some cases, however, you'd like to create opt-in notifications. To do so, modify your notification class and add a function called `getOptInChannels`:

```php
<?php

namespace App\Notifications;

// ...

class InvoicePaid extends Notification
{
    public function via($notifiable)
    {
        return ['mail', 'sms'];
    }

    public function getOptInChannels()
    {
        return ['sms'];
    }
}
```

The package will now **always** suppress the `sms` channel unless the user is explicitly subscribed to it.

### Subscribing

To subscribe a user to an opt-in notification or resubscribe them to a previously unsubscribed notification:

```php
use App\Notifications\InvoicePaid;

$user->subscribe(InvoicePaid::class);
```

Similarly, you can apply a channel:

```php
use App\Notifications\InvoicePaid;

$user->subscribe(InvoicePaid::class, 'mail');
```

### Resetting subscriptions

This package makes no assumptions about how your application manages notifications and subscriptions. For example, if you unsubscribe a user from a particular notification channel, and later subscribe them to all channels, the previous record won't be deleted. To reset the notifications on a user:

```php
use App\Notifications\InvoicePaid;

$user->resetSubscriptions(InvoicePaid::class);
```

You can chain the `resetSubscriptions`:

```php
use App\Notifications\InvoicePaid;

$user->resetSubscriptions(InvoicePaid::class)->subscribe(InvoicePaid::class);
```

### Retrieving subscriptions

You can get a user's subscriptions by using the `notificationSubscriptions()` relation:

```php
$user->notificationSubscriptions();
```

## Model scoping

In some applications, you need to unsubscribe users from notifications related to a certain model. For example, if a user is a part of multiple organizations, they may only want to unsubscribe from a single organization. You can accomplish this by applying a model scope to your notifications:

```php
use App\Models\Organization;
use App\Notifications\InvoicePaid;

//...

$organization = Organization::find(1);

$user->unsubscribe(InvoicePaid::class, '*', $organization);
```

Or, for a single channel:

```php
use App\Models\Organization;
use App\Notifications\InvoicePaid;

//...

$organization = Organization::find(1);

$user->unsubscribe(InvoicePaid::class, 'mail', $organization);
```

Next, we need a way to retrieve the `Organization` when your notification is sent. Add a function called `getSubscriptionModel` to your notification class to tell it how to retrieve the model:

```php
<?php

namespace App\Notifications;

// ...

class InvoicePaid extends Notification
{
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function getSubscriptionModel($notifiable)
    {
        return $this->invoice->organization;
    }
}
```

Now, when this notification gets sent, it will check for the model scope and apply it if necessary. You can add your own logic to `getSubscriptionModel` and even return `null` in cases you don't want to scope the subscription.

### Resetting scoped subscriptions

To reset the notifications on a scoped subscription:

```php
use App\Models\Organization;
use App\Notifications\InvoicePaid;

//...

$organization = Organization::find(1);

$user->resetSubscriptions(InvoicePaid::class, $organization);
```

### Retrieving scoped subscriptions

Retrieve subscriptions related to a certain model:

```php
use App\Models\Organization;

//...

$organization = Organization::find(1);

$user->notificationSubscriptions()->model($organization);
```

## Advanced usage

### Ignoring subscriptions

If you'd like the package to ignore your notification entirely, and skip any suppressions, set the public `$ignoreSubscriptions` property to true in your notification class:

```php
<?php

namespace App\Notifications;

// ...

class InvoicePaid extends Notification
{
    public function __construct(Invoice $invoice, $ignore = false)
    {
        $this->ignoreSubscriptions = $ignore;
    }
}
```

```php
use App\Notifications\InvoicePaid;

$user->notify(new InvoicePaid($invoice, true)); //This will always get sent.
```

### Excluding channels

You may want to exclude certain channels from being considered when checking for unsubscribes. By default, we already exclude the `database` channel. You can configure this in the configuration file:

```php
<?php

return [
	
	'excluded_channels' => ['database'],

];
```

## Resolution logic

The package uses the following logic to resolve whether or not to send a notification:

1. If `channel` is in `excluded_channels`, send the notification.
2. If the notification has the public property `$ignoreSubscriptions` set to `true`, send the notification.
3. Attempt to retrieve a record for the particular channel, if none is found, attempt to retrieve a record for all channels (i.e. `"*"`).

   3a. If there is no record, and the channel is not opt-in, send the notification.

   3b. If there is a record, send the notification based on the status of the subscription (subscribed or unsubscribed).

## License
Released under the [MIT](https://choosealicense.com/licenses/mit/) license. See [LICENSE](LICENSE.md) for more information.