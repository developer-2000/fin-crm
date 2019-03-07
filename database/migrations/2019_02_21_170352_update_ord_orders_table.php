<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrdOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ord_orders', function (Blueprint $table) {
            $table->integer('amount')->default(0)->index()->after('subproject_id')->comment('кол-во заказа');
            $table->integer('country')->default(0)->index()->after('status2')->comment('страна заказчика');
            $table->renameColumn('status1', 'status_moderation');
            $table->renameColumn('status2', 'status_financial');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ord_orders', function (Blueprint $table) {
            //
        });
    }
}
