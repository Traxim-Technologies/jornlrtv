<?php

use Illuminate\Database\Seeder;

class AddedResolutionsInSettingsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
         	[
	            'key' => 'VIDEO_RESOLUTIONS',
			    'value' => '426x240',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => 'VIDEO_RESOLUTIONS',
			    'value' => '640x360',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => 'VIDEO_RESOLUTIONS',
			    'value' => '854x480',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => 'VIDEO_RESOLUTIONS',
			    'value' => '1280x720',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => 'VIDEO_RESOLUTIONS',
			    'value' => '1920x1080',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ]
        ]);
    }
}
