<?php

use Illuminate\Database\Seeder;

class InstallationSeeder extends Seeder
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
		        'key' => 'installation_process',
		        'value' => 0
		    ],
		]);
    }
}
