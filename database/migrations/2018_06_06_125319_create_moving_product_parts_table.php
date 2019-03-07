<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovingProductPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moving_product_parts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('mp_id');
            $table->unsignedInteger('amount');
            $table->unsignedTinyInteger('status')->default(0)->index();

            $table->foreign('mp_id')->references('id')->on('moving_product')->onDelete('cascade');
        });

//        DB::statement('truncate table movings');
        Schema::table('movings', function(Blueprint $table) {
            $table->unsignedTinyInteger('status')->default(0)->index();
            $table->unsignedInteger('user_id')->after('id');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->before('send_date');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
