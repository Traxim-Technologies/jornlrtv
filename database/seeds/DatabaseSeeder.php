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
        $this->call(AdminSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(MobileRegisterSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RedeemSeeder::class);
        $this->call(ScriptSettingSeeder::class);
        $this->call(MultiChannelSeeder::class);
        $this->call(AdminDemoLoginSeeder::class);
        $this->call(VideoSettingsSeeder::class);
    }
}
