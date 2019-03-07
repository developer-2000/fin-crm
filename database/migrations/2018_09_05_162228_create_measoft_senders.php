<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeasoftSenders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measoft_senders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('target_id')->index();
            $table->integer('sub_project_id')->index();
            $table->string('name');
            $table->string('extra');
            $table->string('login');
            $table->string('password');
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
        Schema::dropIfExists('measoft_senders');
    }
}
