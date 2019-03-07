<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStorecurrentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storecurrents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('storehouse_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('amount');
            $table->timestamps();

            $table->foreign('storehouse_id')->references('id')->on('storehouses')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storecurrents');
    }
}
