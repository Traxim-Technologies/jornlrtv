<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayPerViewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_videos', function (Blueprint $table) {
            $table->integer('type_of_user')->default(0)->after('watch_count');
            $table->integer('type_of_subscription')->default(0)->after('type_of_user');
            $table->integer('amount')->default(0)->after('type_of_subscription');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_videos', function (Blueprint $table) {
            $table->dropColumn('type_of_user');
            $table->dropColumn('type_of_subscription');
            $table->dropColumn('amount');
        });
    }
}
