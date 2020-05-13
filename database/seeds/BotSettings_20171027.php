<?php

use Illuminate\Database\Seeder;

class BotSettings_20171027 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bot_settings')->insert([
            'text' => '‼️ {product_name} доступен для предзаказа в Booking',
            'type' => 'Приход модели Booking',
            'code' => 'message',
            'shortcodes' => '{product_name} {count}',
        ]);

        DB::table('bot_settings')->where('id',61)->update([
            'text' => "Общая сумма: {total} руб.
- технотел: {technotel_total} руб.
- букинг: {booking_total} руб.
Общее количество: {quantity} шт.
- технотел: {technotel_quantity} шт.
- букинг: {booking_quantity} шт.",
            'type' => 'Статистика товаров для админа',
            'code' => 'message',
            'shortcodes' => '{total} {technotel_total} {booking_total} {count} {technotel_count} {booking_count} {quantity} {technotel_quantity} {booking_quantity}',
        ]);
    }
}
