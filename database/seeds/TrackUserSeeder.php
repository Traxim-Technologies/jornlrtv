<?php

use Illuminate\Database\Seeder;

class TrackUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

       	DB::table('settings')->insert([
    		[
		        'key' => 'track_user_mail',
		        'value' => ''
		    ],
		]);
    }
}
