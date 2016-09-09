<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingsTableSeeder::class);
        $this->call(AddThemeSeeder::class);
        $this->call(MobileRegisterSeeder::class);
        $this->call(S3Seeder::class);
        $this->call(StreamingSeeder::class);
    }
}
