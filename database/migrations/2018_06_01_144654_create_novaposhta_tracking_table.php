<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNovaposhtaTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('novaposhta_tracking', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('status_code');
            $table->string('status');
            $table->bigInteger('track');
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
        Schema::dropIfExists('novaposhta_tracking');
    }
}
