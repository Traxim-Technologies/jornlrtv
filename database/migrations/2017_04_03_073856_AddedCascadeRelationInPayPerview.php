<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedCascadeRelationInPayPerview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_per_views', function (Blueprint $table) {
            DB::statement('ALTER TABLE pay_per_views DROP FOREIGN KEY pay_per_views_user_id_foreign'); 
            DB::statement('ALTER TABLE pay_per_views ADD CONSTRAINT pay_per_views_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE');
            DB::statement('ALTER TABLE pay_per_views DROP FOREIGN KEY pay_per_views_video_id_foreign');
            DB::statement('ALTER TABLE pay_per_views ADD CONSTRAINT pay_per_views_video_id_foreign FOREIGN KEY (video_id) REFERENCES admin_videos(id) ON DELETE CASCADE ON UPDATE CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_per_views', function (Blueprint $table) {
            //
        });
    }
}
