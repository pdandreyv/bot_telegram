<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMailingsToSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => '',
            'type' => 'Xiaomi',
            'code' => 'mailing',
            'shortcodes' => 'fa-mobile',
        ]);

        DB::table('bot_settings')->insert([
            'text' => '',
            'type' => 'Xiaomi (опт)',
            'code' => 'mailing',
            'shortcodes' => 'fa-mobile',
        ]);

        DB::table('bot_settings')->insert([
            'text' => '',
            'type' => 'Huawei',
            'code' => 'mailing',
            'shortcodes' => 'fa-mobile',
        ]);

        DB::table('bot_settings')->insert([
            'text' => '',
            'type' => 'Huawei (опт)',
            'code' => 'mailing',
            'shortcodes' => 'fa-mobile',
        ]);

        DB::table('bot_settings')->insert([
            'text' => '',
            'type' => 'LG',
            'code' => 'mailing',
            'shortcodes' => 'fa-mobile',
        ]);

        DB::table('bot_settings')->insert([
            'text' => '',
            'type' => 'LG (опт)',
            'code' => 'mailing',
            'shortcodes' => 'fa-mobile',
        ]);
    }


    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Xiaomi')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Xiaomi (опт)')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Huawei')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Huawei (опт)')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'LG')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'LG (опт)')
            ->delete();
    }
}
