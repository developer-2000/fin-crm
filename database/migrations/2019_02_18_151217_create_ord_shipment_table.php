<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdShipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ord_shipment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->default(0)->index()->comment('связь с ord_orders');
            $table->integer('product_id')->default(0)->index()->comment('какой товар из storage_contents или product_projects - зависит от negative таблицы projects_new');
            $table->integer('amount')->default(0)->index()->comment('кол-во отправленного');
            $table->tinyInteger('color_id')->default(0)->index()->comment('номер цвета, значение в моделе ord_orders');
            $table->text('coment')->nullable();
            $table->string('url_document')->nullable()->comment('прикрепленный документ к отправке');
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
        Schema::dropIfExists('ord_shipment');
    }
}
