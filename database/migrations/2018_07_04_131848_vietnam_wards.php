<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VietnamWards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vietnam_wards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ward_id')->index()->nullable();
            $table->string('ward_name')->index()->nullable();
            $table->integer('district_id')->index()->nullable();
            $table->integer('district_value')->index()->nullable();
            $table->string('district_name')->index()->nullable();
            $table->integer('province_id')->index()->nullable();
            $table->string('province_name')->index()->nullable();
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
        Schema::dropIfExists('vietnam_wards');
    }
}
