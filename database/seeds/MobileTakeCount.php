<?php

use Illuminate\Database\Seeder;

class MobileTakeCount extends Seeder
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
        		'key' => 'admin_take_count',
        		'value' => 12
        	]
        ]);
    }
}
