<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoragemovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storagemoves', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('sender_id')->nullable()->default(NULL);
            $table->unsignedInteger('receiver_id')->nullable()->default(NULL);
            $table->timestamp('send_date')->nullable()->default(NULL);
            $table->timestamp('received_date')->nullable()->default(NULL);

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storagemoves');
    }
}
