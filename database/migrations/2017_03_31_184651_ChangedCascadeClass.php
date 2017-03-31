<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangedCascadeClass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flags', function (Blueprint $table) {
            DB::statement('ALTER TABLE flags DROP FOREIGN KEY flags_user_id_foreign'); 
            DB::statement('ALTER TABLE flags ADD CONSTRAINT flags_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE');
            DB::statement('ALTER TABLE flags DROP FOREIGN KEY flags_video_id_foreign');
            DB::statement('ALTER TABLE flags ADD CONSTRAINT flags_video_id_foreign FOREIGN KEY (video_id) REFERENCES admin_videos(id) ON DELETE CASCADE ON UPDATE CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flags', function (Blueprint $table) {
            //
        });
    }
}
