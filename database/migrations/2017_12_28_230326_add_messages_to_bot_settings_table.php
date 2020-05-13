<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMessagesToBotSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => 'Выберите город:',
            'type' => 'Список городов',
            'code' => 'message',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Категория {category_name} начала работу.',
            'type' => 'Начало работы категории',
            'code' => 'mailing',
            'shortcodes' => '{category_name}',
        ]);
    }


    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Список городов')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Начало работы категории')
            ->delete();
    }
}
