<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeColumnsToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('time_sent')->index()->nullable();
            $table->dateTime('time_at_department')->index()->nullable();
            $table->dateTime('time_received')->index()->nullable();
            $table->dateTime('time_returned')->index()->nullable();
            $table->dateTime('time_paid_up')->index()->nullable();
            $table->dateTime('time_refused')->index()->nullable();
            $table->dateTime('time_status_updated')->index()->nullable();
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
