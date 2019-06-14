<?php

use Illuminate\Database\Seeder;

class V5Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Schema::hasTable('settings')) {

	  //       DB::table('settings')->insert([
	  //   		[
			//         'key' => 'is_account_email_verification', //Not used
			//         'value' => 1,
			//         'created_at' => date('Y-m-d H:i:s'),
			//         'updated_at' => date('Y-m-d H:i:s')
			//     ],
			// ]);

		}
    }
}
