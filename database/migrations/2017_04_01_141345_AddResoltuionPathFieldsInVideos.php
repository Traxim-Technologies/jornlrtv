<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResoltuionPathFieldsInVideos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_videos', function (Blueprint $table) {
             $table->longText('video_resize_path')->nullable()->after('trailer_compress_status');
             $table->longText('trailer_resize_path')->nullable()->after('video_resize_path');
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
            $table->dropColumn('video_resize_path');
            $table->dropColumn('trailer_resize_path');
        });
    }
}
