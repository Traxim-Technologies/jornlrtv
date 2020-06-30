<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelSubscriptionPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_subscription_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('channel_id');
            $table->string('payment_id');
            $table->string('payment_mode');
            $table->float('amount');
            $table->string('currency')
            $table->tinyInteger('is_current')->default(0);
            $table->tinyInteger('is_cancelled');
            $table->text('cancel_reason');
            $table->tinyInteger('is_coupon_applied')->default(0);
            $table->string('coupon_code');
            $table->float('coupon_amount')->default(0.00);
            $table->dateTime('expiry_date');
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
        Schema::drop('channel_subscription_payments');
    }
}
