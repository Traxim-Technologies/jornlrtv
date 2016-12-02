<?php

use Illuminate\Database\Seeder;

class DeleteControlSeeder extends Seeder
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
        		'key' => 'admin_delete_control',
        		'value' => 1
        	]
        ]);
    }
}
