<?php

use Illuminate\Database\Seeder;

class ChannelSettingsSeeder extends Seeder
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
		        'key' => 'channel_create_by_user',
		        'value' => 1
		    ],
		    [
		        'key' => 'broadcast_by_user',
		        'value' => 1
		    ],
		    [
		        'key' => 'master_user_login',
		        'value' => 1
		    ]
		]);
    }
}
