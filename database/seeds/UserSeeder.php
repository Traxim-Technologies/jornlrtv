<?php

use Illuminate\Database\Seeder;

use App\Helpers\Helper;

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
                'token'=>Helper::generate_token(),
                'token_expiry'=>Helper::generate_token_expiry(),
                'is_verified'=>1,
		        'created_at' => date('Y-m-d H:i:s'),
		        'updated_at' => date('Y-m-d H:i:s')
		    ],
		]);
    }
}
