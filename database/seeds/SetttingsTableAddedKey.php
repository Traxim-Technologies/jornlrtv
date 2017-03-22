<?php

use Illuminate\Database\Seeder;

class SetttingsTableAddedKey extends Seeder
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
            'key' => 'admin_theme_control',
		    'value' => 1,
		    'created_at' => date('Y-m-d H:i:s'),
		    'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
