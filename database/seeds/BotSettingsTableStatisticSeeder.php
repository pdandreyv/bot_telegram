<?php

use Illuminate\Database\Seeder;

class BotSettingsTableStatisticSeeder extends Seeder
{

    public function run()
    {
        DB::table('bot_settings')->insert([
            'text' => 'Статистика',
            'type' => 'Статистика',
            'code' => 'button',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Обновить',
            'type' => 'Обновить',
            'code' => 'button',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Сумма: {sum}. Количество позиций: {products_quantity}. Количество всего: {total_quantity}.',
            'type' => 'Статистика товаров для админа',
            'code' => 'message',
            'shortcodes' => '{sum} {products_quantity} {total_quantity}',
        ]);
    }
}
