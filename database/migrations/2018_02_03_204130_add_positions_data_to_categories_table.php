<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositionsDataToCategoriesTable extends Migration
{
    public function up()
    {
        DB::table('categories')
            ->where('name', '=', 'Apple')
            ->update([
                'position' => 1,
        ]);

        DB::table('categories')
            ->where('name', '=', 'Samsung')
            ->update([
                'position' => 2,
            ]);

        DB::table('categories')
            ->where('name', '=', 'Booking')
            ->update([
                'position' => 3,
            ]);

        DB::table('categories')
            ->where('name', '=', 'Xiaomi')
            ->update([
                'position' => 4,
            ]);

        DB::table('categories')
            ->where('name', '=', 'Huawei')
            ->update([
                'position' => 5,
            ]);

        DB::table('categories')
            ->where('name', '=', 'Уцененный товар ?')
            ->update([
                'position' => 6,
            ]);

        DB::table('categories')
            ->where('name', '=', '? iPhone')
            ->update([
                'position' => 10,
            ]);

        DB::table('categories')
            ->where('name', '=', '?? iPhone Rfb')
            ->update([
                'position' => 20,
            ]);

        DB::table('categories')
            ->where('name', '=', '? iPhone SE/6/6s')
            ->update([
                'position' => 30,
            ]);

        DB::table('categories')
            ->where('name', '=', '? iPhone 7/7 plus')
            ->update([
                'position' => 40,
            ]);

        DB::table('categories')
            ->where('name', '=', '? iPhone 8/8 plus')
            ->update([
                'position' => 50,
            ]);

        DB::table('categories')
            ->where('name', '=', '? iPhone X')
            ->update([
                'position' => 60,
            ]);

        DB::table('categories')
            ->where('name', '=', '? iPad + Macbook')
            ->update([
                'position' => 70,
            ]);

        DB::table('categories')
            ->where('name', '=', '⌚️ iWatch')
            ->update([
                'position' => 80,
            ]);

        DB::table('categories')
            ->where('name', '=', '? iPhone 8')
            ->update([
                'position' => 10,
            ]);

        DB::table('categories')
            ->where('name', '=', '? iPhone 8 plus')
            ->update([
                'position' => 20,
            ]);

        DB::table('categories')
            ->where('name', '=', '⌚️ iWatch (new)')
            ->update([
                'position' => 30,
            ]);


    }


    public function down()
    {

    }
}
