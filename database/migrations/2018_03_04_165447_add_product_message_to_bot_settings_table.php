<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductMessageToBotSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('bot_settings', function (Blueprint $table) {
            DB::table('bot_settings')->insert([
                'text' => "{product_name} появился на складе.",
                'type' => 'Сообщение о добавлении товара на склад',
                'code' => 'message',
                'shortcodes' => '{product_name}',
            ]);
        });
    }


    public function down()
    {
        Schema::table('bot_settings', function (Blueprint $table) {
            DB::table('bot_settings')
                ->where('type', 'Сообщение о добавлении товара на склад')
                ->delete();
        });
    }
}
