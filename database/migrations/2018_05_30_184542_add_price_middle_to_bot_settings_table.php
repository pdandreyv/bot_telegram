<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceMiddleToBotSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('bot_settings', function (Blueprint $table) {
            DB::table('bot_settings')->insert([
                'text' => '',
                'type' => 'Apple (средний опт)',
                'code' => 'mailing',
                'shortcodes' => 'fa-mobile',
            ]);

            DB::table('bot_settings')->insert([
                'text' => '',
                'type' => 'Samsung (средний опт)',
                'code' => 'mailing',
                'shortcodes' => 'fa-mobile',
            ]);

            DB::table('bot_settings')->insert([
                'text' => '',
                'type' => 'Booking (средний опт)',
                'code' => 'mailing',
                'shortcodes' => 'fa-mobile',
            ]);

            DB::table('bot_settings')->insert([
                'text' => '',
                'type' => 'Xiaomi (средний опт)',
                'code' => 'mailing',
                'shortcodes' => 'fa-mobile',
            ]);

            DB::table('bot_settings')->insert([
                'text' => '',
                'type' => 'Huawei (средний опт)',
                'code' => 'mailing',
                'shortcodes' => 'fa-mobile',
            ]);

            DB::table('bot_settings')->insert([
                'text' => '',
                'type' => 'LG (средний опт)',
                'code' => 'mailing',
                'shortcodes' => 'fa-mobile',
            ]);
        });
    }


    public function down()
    {
        Schema::table('bot_settings', function (Blueprint $table) {
            //
        });
    }
}
