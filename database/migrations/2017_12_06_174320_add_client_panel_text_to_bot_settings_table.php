<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientPanelTextToBotSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => 'Панель управления клиентом:',
            'type' => 'Панель управления клиентом',
            'code' => 'message',
        ]);
    }


    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Панель управления клиентом')
            ->delete();
    }
}
