<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ViettelSenders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viettel_senders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('viettel_key_id')->index();
            $table->integer('customer_id')->index();
            $table->integer('warehouse_id')->index();
            $table->string('name');
            $table->string('address');
            $table->integer('phone')->index();
            $table->integer('post_id')->index();
            $table->integer('province_id')->index();
            $table->integer('district_id')->index();
            $table->integer('wards_id')->index();
            $table->integer('role')->index();
            $table->string('province_name');
            $table->string('district_name');
            $table->string('wards_name');
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
        Schema::dropIfExists('viettel_senders');
    }
}
