<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ord_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_user')->default(0)->index()->comment('человек заказавший товар');
            $table->integer('executor_user')->default(0)->index()->comment('человек поставщик');
            $table->integer('project_id')->default(0)->index();
            $table->integer('subproject_id')->default(0)->index()->comment('локация доставки товара');
            $table->tinyInteger('status1')->default(0)->index();
            $table->tinyInteger('status2')->default(0)->index();
            $table->timestamp('creat_order')->comment('дата создания заказа');
            $table->timestamp('payment_order')->comment('дата оплаты заказа');
            $table->timestamp('shipment_order')->comment('дата отгрузки заказа');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ord_orders');
    }
}
