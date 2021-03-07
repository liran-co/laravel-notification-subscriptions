<?php

namespace LiranCo\NotificationSubscriptions\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSubscription extends Model
{
    public $incrementing = true;

    protected $table = 'notification_subscriptions';

    protected $fillable = ['type', 'channel', 'model_type', 'model_id', 'unsubscribed_at'];

    protected $casts = [
        'unsubscribed_at' => 'datetime',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function scopeModel($query, $model = null)
    {
        return $query->where('model_type', $model ? get_class($model) : null)->where('model_id', optional($model)->id);
    }

    public function isSubscribed()
    {
        return is_null($this->unsubscribed_at);
    }

    public function unsubscribe()
    {
        $this->forceFill(['unsubscribed_at' => $this->freshTimestamp()])->save();
    }

    public function resubscribe()
    {
        $this->forceFill(['unsubscribed_at' => null])->save();
    }
}
