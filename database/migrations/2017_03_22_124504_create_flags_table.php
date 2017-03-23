<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flags', function (Blueprint $table) {
            $table->increments('id')->comment('Primary Key, It is an unique key');
            $table->integer('user_id')->unsigned()->index()->comment('User table Primary key given as Foreign Key');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('video_id')->unsigned()->index()->comment('Admin Video table Primary key given as Foreign Key');
            $table->foreign('video_id')->references('id')->on('admin_videos');
            $table->longText('reason')->nullable()->comment('Reason for flagging the video');
            $table->smallInteger('status')->default(0)->comment('Status of the flag table');
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
        Schema::drop('flags');
    }
}
