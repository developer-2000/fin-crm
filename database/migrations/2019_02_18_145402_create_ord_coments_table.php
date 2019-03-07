<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdComentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ord_coments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->default(0)->index()->comment('связь с ord_orders');
            $table->integer('user_id')->default(0)->index()->comment('связь с users');
            $table->text('text')->nullable();
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
        Schema::dropIfExists('ord_coments');
    }
}
