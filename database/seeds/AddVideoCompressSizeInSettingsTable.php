<?php

use Illuminate\Database\Seeder;

class AddVideoCompressSizeInSettingsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'key' => 'video_compress_size',
		    'value' => 2,
		    'created_at' => date('Y-m-d H:i:s'),
		    'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
