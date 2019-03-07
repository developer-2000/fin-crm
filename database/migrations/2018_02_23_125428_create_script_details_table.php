<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScriptDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('script_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('script_id')->index();
            $table->integer('position')->index();
            $table->integer('script_category_id')->index();
            $table->string('block');
            $table->string('status')->index();
            $table->text('text');
            $table->string('img')->nullable();

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
        Schema::drop('script_details');
    }
}
