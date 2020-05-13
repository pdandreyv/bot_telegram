<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientPanelButtonsToBotSettingsTable extends Migration
{

    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => 'Отключить',
            'type' => 'Отключить',
            'code' => 'button',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'История',
            'type' => 'История',
            'code' => 'button',
        ]);
    }


    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Отключить')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'История')
            ->delete();
    }
}
