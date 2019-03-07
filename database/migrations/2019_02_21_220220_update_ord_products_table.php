<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrdProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ord_products', function (Blueprint $table) {
            $table->integer('color_amount')->default(0)->after('color_id')->comment('количество заказа этого цвета');
            $table->dropColumn('amount');
            $table->integer('order_id')->unsigned()->change();
            $table->foreign('order_id')->references('id')->on('ord_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ord_products', function (Blueprint $table) {
            //
        });
    }
}
