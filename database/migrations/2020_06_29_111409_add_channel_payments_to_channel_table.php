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
            $table->integer('paid_channel')->default(0);
            $table->integer('total_amount')->after('paid_channel')->default(0.00);
            $table->integer('admin_commission');
            $table->integer('owner_commission');
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
        });
    }
}
