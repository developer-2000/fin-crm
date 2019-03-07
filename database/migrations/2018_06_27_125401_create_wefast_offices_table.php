<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWefastOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wefast_offices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('district_code')->index();
            $table->string('district_name')->index();
            $table->string('province_code')->index();
            $table->string('province_name')->index();
            $table->string('ward_code')->index();
            $table->string('ward_name')->index();
            $table->tinyInteger('pickup')->index();
            $table->tinyInteger('delivery')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wefast_offices');
    }
}
