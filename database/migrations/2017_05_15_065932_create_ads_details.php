<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ads_id');
            $table->integer('ad_type')->nullable()->comment('1 - Pre Ad, 2 - Post Ad, 3 - In Between Ad');
            $table->time('video_time');
            $table->integer('ad_time')->default(0)->comment('In Seconds');
            $table->string('file')->nullable();
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
        Schema::drop('ads_details');
    }
}
