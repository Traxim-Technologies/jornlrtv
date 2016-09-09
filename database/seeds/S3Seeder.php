<?php

use Illuminate\Database\Seeder;

class S3Seeder extends Seeder
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
		        'key' => 's3_key',
		        'value' => ''
		    ],
		    [
		        'key' => 's3_secret',
		        'value' => ''
		    ],
		    [
		        'key' => 's3_region',
		        'value' => ''
		    ],
		    [
		        'key' => 's3_bucket',
		        'value' => ''
		    ],
		]);
    }
}
