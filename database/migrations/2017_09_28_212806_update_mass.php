<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mass_messages', function (Blueprint $table) {
            $table->text('keyboards')->after('message');
            $table->text('sent_ids')->after('message');
            $table->text('clients_ids')->after('message');
            $table->dropColumn('last_client_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
