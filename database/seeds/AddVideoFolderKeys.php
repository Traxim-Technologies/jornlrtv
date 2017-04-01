<?php

use Illuminate\Database\Seeder;

class AddVideoFolderKeys extends Seeder
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
	            'key' => 'original_key',
			    'value' => 'original',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
         	[
	            'key' => '426x240_key',
			    'value' => 'mp4240',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => '640x360_key',
			    'value' => 'mp4360',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => '854x480_key',
			    'value' => 'mp4480',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => '1280x720_key',
			    'value' => 'mp4720',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => '1920x1080_key',
			    'value' => 'mp41080',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ]
        ]);
    }
}
