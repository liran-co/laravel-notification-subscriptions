<?php

namespace LiranCo\NotificationSubscriptions\Traits;

use LiranCo\NotificationSubscriptions\Models\NotificationSubscription;

trait HasNotificationSubscriptions
{
    public function notificationSubscriptions()
    {
        return $this->morphMany(NotificationSubscription::class, 'notifiable');
    }

    public function model()
    {
        return $this->morphMany(NotificationSubscription::class, 'model');
    }

    public function subscribe($type, $channel = '*', $model = null)
    {
        $subscription = $this->findSubscription($type, $channel, $model);

        if ($subscription) {
            return $subscription->resubscribe();
        }

        return $this->createSubscription($type, $channel, $model);
    }

    public function unsubscribe($type, $channel = '*', $model = null)
    {
        $subscription = $this->findSubscription($type, $channel, $model);

        if ($subscription) {
            return $subscription->unsubscribe();
        }

        return $this->createSubscription($type, $channel, $model, true);
    }

    public function findSubscription($type, $channel = '*', $model = null)
    {
        return $this->notificationSubscriptions()->where('type', $type)->where('channel', $channel)->model($model)->first();
    }

    public function createSubscription($type, $channel = '*', $model = null, $unsubscribe = false)
    {
        return $this->notificationSubscriptions()->create([
            'type'            => $type,
            'channel'         => $channel,
            'model_type'      => $model ? get_class($model) : null,
            'model_id'        => optional($model)->id,
            'unsubscribed_at' => $unsubscribe ? $this->freshTimestamp() : null,
        ]);
    }

    public function isSubscribed($type, $channel, $model = null, $optin = [])
    {
        $subscription = $this->findSubscription($type, $channel, $model) ?: $this->findSubscription($type, '*', $model);

        if (!$subscription) {
            return !in_array($channel, $optin);
        }

        return $subscription->isSubscribed();
    }

    public function resetSubscriptions($type, $model = null)
    {
        $this->notificationSubscriptions()->where('type', $type)->model($model)->delete();

        return $this;
    }
}
