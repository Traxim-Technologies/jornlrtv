<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayPerViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_per_views', function (Blueprint $table) {
            $table->increments('id')->comment('Primary Key, It is an unique key');
            $table->integer('user_id')->unsigned()->index()->comment('User table Primary key given as Foreign Key');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('video_id')->unsigned()->index()->comment('Admin Video table Primary key given as Foreign Key');
            $table->foreign('video_id')->references('id')->on('admin_videos');
            $table->string('payment_id');
            $table->float('amount');
            $table->dateTime('expiry_date');
            $table->smallInteger('status')->default(0)->comment('Status of the per_per_view table');
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
        Schema::drop('pay_per_views');
    }
}
