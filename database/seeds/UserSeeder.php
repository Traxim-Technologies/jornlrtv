<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
    	DB::table('users')->insert([
    		[
		        'name' => 'User',
		        'email' => 'user@streamhash.com',
		        'password' => \Hash::make('123456'),
		        'picture' =>"",
                'is_verified'=>1,
		        'created_at' => date('Y-m-d H:i:s'),
		        'updated_at' => date('Y-m-d H:i:s')
		    ],
		]);
    }
}
