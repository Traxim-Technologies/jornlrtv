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
        $this->call(AddedMaxsizekeysInSettings::class);
        $this->call(AddedLanguageControlInSettings::class);
        $this->call(AddedStripeKeyInSettings::class);
        $this->call(AddedAgeKeyInSettings::class);
        $this->call(RegisterAgeLimitSeeder::class);
        $this->call(AddedSliderKeys::class);
        $this->call(ChannelSettingsSeeder::class);
        $this->call(PayperviewCommissionSplit::class);
        $this->call(PayperViewInSetings::class);
        $this->call(AddSocialLinksSeeder::class);
        $this->call(AppLinkSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(PushNotificationSeeder::class);
        $this->call(SecureVideoSeeder::class);
        $this->call(MailGunSeeder::class);
    }
}
