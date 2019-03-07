<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('status')->index();
            $table->string('type_object')->index();
            $table->integer('company_id')->index();
            $table->string('type_method')->index();
            $table->string('basis_for_calculation')->nullable()->index();

            $table->json('new_prices')->nullable();
            $table->json('criteria');
            $table->json('compare_operator');
            $table->string('interval')->nullable()->index();

            $table->integer('success_plan')->nullable()->index();

            $table->string('comment')->nullable();

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
        Schema::drop('plans');
    }

}
