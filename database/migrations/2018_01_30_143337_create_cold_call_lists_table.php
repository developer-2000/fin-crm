<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColdCallListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cold_call_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cold_call_file_id');
            $table->string('phone_number', 20);
            $table->json('add_info')->nullable();
            $table->integer('proc_status');
            $table->integer('order_id');

            $table->index('cold_call_file_id');
            $table->index('phone_number');
            $table->index('proc_status');
            $table->index( 'order_id');

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
        Schema::drop('cold_call_lists');
    }
}
