<?php

use Illuminate\Database\Seeder;

class AddedReportVideoSettingsTable extends Seeder
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
	            'key' => 'REPORT_VIDEO',
			    'value' => 'Sexual content',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => 'REPORT_VIDEO',
			    'value' => 'Violent or repulsive content.',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => 'REPORT_VIDEO',
			    'value' => 'Hateful or abusive content.',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => 'REPORT_VIDEO',
			    'value' => 'Harmful dangerous acts.',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => 'REPORT_VIDEO',
			    'value' => 'Child abuse.',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => 'REPORT_VIDEO',
			    'value' => 'Spam or misleading.',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => 'REPORT_VIDEO',
			    'value' => 'Infringes my rights.',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],
		    [
	            'key' => 'REPORT_VIDEO',
			    'value' => 'Captions issue.',
			    'created_at' => date('Y-m-d H:i:s'),
			    'updated_at' => date('Y-m-d H:i:s')
		    ],

        ]);

    }
}
