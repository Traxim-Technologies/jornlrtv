<?php

use Illuminate\Database\Seeder;

class AddThemeSeeder extends Seeder
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
		        'key' => 'theme',
		        'value' => 'streamtube'
		    ],
		    [
		        'key' => 'paypal_client_id',
		        'value' => ''
		    ],
		    [
		        'key' => 'paypal_secret',
		        'value' => ''
		    ],
		    [
		        'key' => 'amount',
		        'value' => 10
		    ],
		    [
		        'key' => 'expiry_days',
		        'value' => 28
		    ],
		    [
		        'key' => 'google_analytics',
		        'value' => ""
		    ],
		    [
		        'key' => 'paypal_email',
		        'value' => ""
		    ],


		]);
    }
}
