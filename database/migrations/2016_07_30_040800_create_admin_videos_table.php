<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description');
            $table->integer('video_type');
            $table->integer('video_upload_type');
            $table->string('default_image');
            $table->integer('is_banner');
            $table->string('banner_image');
            $table->time('duration');
            $table->string('video');
            $table->string('trailer_video');
            $table->string('ratings');
            $table->string('reviews');
            $table->dateTime('publish_time');
            $table->enum('uploaded_by',array('admin','moderator','user' ,'other'));
            $table->enum('edited_by',array('admin','moderator','user' ,'other'));
            $table->integer('watch_count');
            $table->integer('is_approved');
            $table->integer('is_home_slider')->default(0)
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
        Schema::drop('admin_videos');
    }
}
