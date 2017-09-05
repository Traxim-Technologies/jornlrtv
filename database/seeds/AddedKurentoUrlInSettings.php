<?php

use Illuminate\Database\Seeder;

class AddedKurentoUrlInSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::table('settings')->insert([
    		[
		        'key' => 'cross_platform_url',
		        'value' => "104.236.1.170:1935",
		    ],
		    [
		        'key' => 'mobile_rtsp',
		        'value' => "rtsp://104.236.1.170:1935/live/",
		    ],
		    [
		        'key' => 'wowza_server_url',
		        'value' => "https://104.236.1.170:8087",
		    ],
		    [
		        'key' => 'kurento_socket_url',
		        'value' => "livetest.streamhash.info:8443",
		    ],
		]);
    }
}
