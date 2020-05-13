<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientNameMessageToBotSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => "Данные успешно сохранены. \r\nИмя: {first_name} \r\nФамилия: {last_name}",
            'type' => 'ФИО клиента',
            'code' => 'message',
            'shortcodes' => '{first_name} {last_name}',
        ]);
    }

    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'ФИО клиента')
            ->delete();
    }
}
