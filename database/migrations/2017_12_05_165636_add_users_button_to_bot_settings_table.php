<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsersButtonToBotSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => '{user_data}',
            'type' => 'Список пользователей',
            'code' => 'button',
            'shortcodes' => '{user_data}',
        ]);
    }


    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Список пользователей')
            ->delete();
    }
}
