<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsersTextToBotSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => 'Для поиска пользователя введите имя:',
            'type' => 'Список клиентов',
            'code' => 'message',
        ]);
    }


    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Список клиентов')
            ->delete();
    }
}
