<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWefastCounterpartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wefast_counterparties', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('key_id')->default(0)->index();
            $table->string('sender')->index();
            $table->string('contact')->index();
            $table->string('phone')->index();
            $table->string('address')->index();
            $table->string('warehouse')->index();
            $table->tinyInteger('active')->index();
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
        Schema::dropIfExists('wefast_counterparties');
    }
}
