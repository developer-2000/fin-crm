<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateInOrdersLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo now() . " orders_log, create tmp\n";
        if (!Schema::hasColumn('orders_log', 'tmp_date')) {
            Schema::table('orders_log', function (Blueprint $table) {
                $table->integer('tmp_date')->nullable();
            });
        }
        echo now() . " update tmp\n";
        \DB::update('UPDATE orders_log SET tmp_date=date');
        echo now() . " drop column\n";
        Schema::table('orders_log', function (Blueprint $table) {
            $table->dropColumn('date');
        });
        echo now() . " create column\n";
        Schema::table('orders_log', function (Blueprint $table) {
            $table->timestamp('date')->useCurrent()->index();
        });
        echo now() . " update column\n";
        \DB::update('UPDATE orders_log SET date=FROM_UNIXTIME(tmp_date)');
        echo now() . " drop tmp\n";
        Schema::table('orders_log', function (Blueprint $table) {
            $table->dropColumn('tmp_date');
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
