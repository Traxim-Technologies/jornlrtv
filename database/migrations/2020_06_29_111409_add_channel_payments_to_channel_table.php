<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChannelPaymentsToChannelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->tinyInteger('is_paid_channel')->default(0);
            $table->float('total_amount')->default(0.00);
            $table->float('subscription_amount')->default(0.00);
            $table->float('admin_commission')->default(0.00);
            $table->float('owner_commission')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channels', function (Blueprint $table) {
            $table->dropColumn('paid_channel');
            $table->dropColumn('total_amount');
            $table->dropColumn('subscription_amount');
            $table->dropColumn('admin_commission');
            $table->dropColumn('owner_commission');
        });
    }
}
