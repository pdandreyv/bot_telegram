<?php

use Illuminate\Database\Seeder;

class BotSettingsTableBookingMailingSeeder extends Seeder
{
    public function run()
    {
        DB::table('bot_settings')->insert([
            'text' => '',
            'type' => 'Booking',
            'code' => 'mailing',
            'shortcodes' => 'fa-apple',
        ]);

        DB::table('bot_settings')->insert([
            'text' => '',
            'type' => 'Booking (опт)',
            'code' => 'mailing',
            'shortcodes' => 'fa-apple',
        ]);
    }
}
