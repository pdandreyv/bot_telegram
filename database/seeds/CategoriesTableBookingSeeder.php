<?php

use Illuminate\Database\Seeder;

class CategoriesTableBookingSeeder extends Seeder
{
    public function run()
    {
        DB::table('categories')->insert([
            'name' => 'Booking',
            'parent_id' => 0,
            'position' => 7,
            'visible' => 1,
        ]);

        DB::table('categories')->insert([
            'name' => 'iPhone 8',
            'parent_id' => 8,
            'position' => 8,
            'visible' => 1,
        ]);

        DB::table('categories')->insert([
            'name' => 'iPhone 8 plus',
            'parent_id' => 8,
            'position' => 9,
            'visible' => 1,
        ]);

        DB::table('categories')->insert([
            'name' => 'iWatch (new)',
            'parent_id' => 8,
            'position' => 10,
            'visible' => 1,
        ]);
    }
}
