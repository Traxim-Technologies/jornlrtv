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
        $this->call(InstallationSeeder::class);
        $this->call(TrackUserSeeder::class);
        $this->call(SetttingsTableAddedKey::class);
        $this->call(AddedReportVideoSettingsTable::class);
        $this->call(AddedResolutionsInSettingsTable::class);
        $this->call(AddedImageResolutionsInSettingsTable::class);
        $this->call(AddVideoCompressSizeInSettingsTable::class);
        $this->call(AddedImageCompressSize::class);
        $this->call(AddVideoFolderKeys::class);
    }
}
