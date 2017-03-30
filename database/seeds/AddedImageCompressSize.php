<?php

use Illuminate\Database\Seeder;

class AddedImageCompressSize extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'key' => 'image_compress_size',
		    'value' => 8,
		    'created_at' => date('Y-m-d H:i:s'),
		    'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
