<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColdCallResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cold_call_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cold_call_list_id');
            $table->string('call_status');
            $table->integer('count_status');

            $table->index('cold_call_list_id');
            $table->index('call_status');
            $table->index('count_status');
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
        Schema::drop('cold_call_results');
    }
}
