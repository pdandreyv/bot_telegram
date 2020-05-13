<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(BotSettingsTableStatisticSeeder::class);
        $this->call(BotSettingsTableBookingMailingSeeder::class);
        $this->call(CategoriesTableBookingSeeder::class);
        $this->call(BotSettings_20171027::class);
    }
}


