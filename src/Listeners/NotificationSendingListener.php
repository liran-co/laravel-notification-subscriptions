<?php

namespace LiranCo\NotificationSubscriptions\Listeners;

use Illuminate\Notifications\Events\NotificationSending;
use LiranCo\NotificationSubscriptions\Events\NotificationSuppressed;
use LiranCo\NotificationSubscriptions\Traits\HasNotificationSubscriptions;

class NotificationSendingListener
{
    public function handle(NotificationSending $event)
    {
        if (!in_array(HasNotificationSubscriptions::class, class_uses_recursive($event->notifiable))) {
            return;
        }

        if (in_array($event->channel, config('notification-subscriptions.excluded_channels'))) {
            return;
        }

        if ($event->notification->ignoreSubscriptions ?? false) {
            return;
        }

        $model = null;
        if (method_exists($event->notification, 'getSubscriptionModel')) {
            $model = $event->notification->getSubscriptionModel($event->notifiable);
        }

        $optin = [];
        if (method_exists($event->notification, 'getOptInSubscriptions')) {
            $optin = $event->notification->getOptInSubscriptions();
        }

        $subscribed = $event->notifiable->isSubscribed(get_class($event->notification), $event->channel, $model, $optin);

        if (!$subscribed) {
            event(new NotificationSuppressed($event));
            return false;
        }

        return;
    }
}
