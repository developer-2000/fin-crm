<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('moderator_id')->index();
            $table->integer('user_id')->index();
            $table->integer('order_id')->index();
            $table->integer('orders_opened_id')->index();
            $table->integer('company_id')->index();
            $table->string('type')->index();
            $table->string('mistakes')->index();
            $table->string('status')->index();
            $table->integer('read')->index();
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
        Schema::drop('feedback');
    }
}
