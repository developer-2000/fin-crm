<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->index();
            $table->integer('plan_id')->index();
            $table->integer('transaction_id')->index();

            $table->integer('company_id')->index();
            $table->integer('user_id')->index();

            $table->float('result')->index();
            $table->string('text');

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
        Schema::drop('plan_logs');
    }
}
