<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->delete();
    	DB::table('settings')->insert([
    		[
		        'key' => 'site_name',
		        'value' => 'StreamHash'
		    ],
		    [
		        'key' => 'site_logo',
		        'value' => ''
		    ],
		    [
		        'key' => 'site_icon',
		        'value' => ''
		    ],
		    [
		        'key' => 'tag_name',
		        'value' => ''
		    ],
		    [
		        'key' => 'paypal_email',
		        'value' => ''
		    ],
		    [
		        'key' => 'browser_key',
		        'value' => ''
		    ],
		    [
		        'key' => 'default_lang',
		        'value' => 'en'
		    ], 
		    [
		        'key' => 'currency',
		        'value' => '$'
		    ],

		]);
    }
}
