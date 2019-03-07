<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKazpostSenders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kazpost_senders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('target_id')->index();
            $table->integer('sub_project_id')->index();
            $table->string('name_last');
            $table->string('name_fm');
            $table->string('city');
            $table->string('address');
            $table->string('index');
            $table->string('code');
            $table->string('doc');
            $table->string('doc_num');
            $table->string('doc_day');
            $table->string('doc_month');
            $table->string('doc_year');
            $table->string('doc_body');
            $table->string('payment_code');
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
        Schema::dropIfExists('kazpost_senders');
    }
}
