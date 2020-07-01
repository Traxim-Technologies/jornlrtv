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
            $table->integer('user_id')->default(0);
            $table->integer('channel_id')->default(0);
            $table->string('payment_id')->default(0);
            $table->string('payment_mode')->default('cod');
            $table->float('amount')->default(0.00);
            $table->string('currency')->default('$');
            $table->tinyInteger('is_current')->default(0);
            $table->tinyInteger('is_cancelled')->default(0);
            $table->text('cancel_reason')->default('');
            $table->tinyInteger('is_coupon_applied')->default(0);
            $table->string('coupon_code')->default('');
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
