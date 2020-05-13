<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCitySavedMessageToBotSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => "Данные успешно сохранены. \r\nГород: {city}",
            'type' => 'Город клиента сохранен',
            'code' => 'message',
            'shortcodes' => '{city}',
        ]);
    }


    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Город клиента сохранен')
            ->delete();
    }
}
