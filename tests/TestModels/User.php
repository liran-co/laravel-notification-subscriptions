<?php


namespace LiranCo\NotificationSubscriptions\Tests\TestModels;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use LiranCo\NotificationSubscriptions\Tests\database\factories\UserFactory;
use LiranCo\NotificationSubscriptions\Traits\HasNotificationSubscriptions;

class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use Authorizable, Authenticatable, HasFactory, HasNotificationSubscriptions, Notifiable;

    protected $guarded = [];

    protected $table = 'users';

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
