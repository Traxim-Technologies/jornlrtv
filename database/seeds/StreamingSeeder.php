<?php

use Illuminate\Database\Seeder;

class StreamingSeeder extends Seeder
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
        		'key' => 'streaming_url',
        		'value' => ''
        	]
        ]);
    }
}
