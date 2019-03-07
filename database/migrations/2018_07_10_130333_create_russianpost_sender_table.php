<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRussianpostSenderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('russianpost_senders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('target_id')->index();
            $table->integer('sub_project_id')->index();
            $table->string('name_first');
            $table->string('name_last');
            $table->string('name_middle');
            $table->string('city');
            $table->string('address');
            $table->string('index');
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
        Schema::dropIfExists('russianpost_senders');
    }
}
