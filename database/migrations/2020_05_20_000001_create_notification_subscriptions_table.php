<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('notification_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('channel');
            $table->morphs('notifiable');
            $table->nullableMorphs('model');
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_subscriptions');
    }
}
