<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrdOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ord_orders', function (Blueprint $table) {
            $table->integer('order_user')->unsigned()->change();
            $table->foreign('order_user')->references('id')->on('users');
            $table->string('creat_order')->nullable()->change();
            $table->string('payment_order')->nullable()->change();
            $table->string('shipment_order')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('OrdOrder', function (Blueprint $table) {
            //
        });
    }
}
