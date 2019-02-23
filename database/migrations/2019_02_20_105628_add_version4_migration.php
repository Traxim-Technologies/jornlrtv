<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVersion4Migration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bell_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_user_id');
            $table->integer('to_user_id');
            $table->string('notification_type');
            $table->text('message');
            $table->integer('channel_id')->default(0);
            $table->integer('video_tape_id')->default(0);
            $table->integer('status')->default(BELL_NOTIFICATION_STATUS_UNREAD);
            $table->timestamps();
        });

        Schema::create('bell_notification_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bell_notifications');

        Schema::drop('bell_notification_templates');

    }
}
