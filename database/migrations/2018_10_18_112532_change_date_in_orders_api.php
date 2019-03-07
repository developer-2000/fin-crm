<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateInOrdersApi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo now() . " orders_api, create tmp\n";
        if (!Schema::hasColumn('orders_api', 'tmp_date')) {
            Schema::table('orders_api', function (Blueprint $table) {
                $table->integer('tmp_date')->nullable();
            });
        }
        echo now() . " update tmp\n";
        \DB::update('UPDATE orders_api SET tmp_date=date');
        echo now() . " drop column\n";
        Schema::table('orders_api', function (Blueprint $table) {
            $table->dropColumn('date');
        });
        echo now() . " create column\n";
        Schema::table('orders_api', function (Blueprint $table) {
            $table->timestamp('date')->index();
        });
        echo now() . " update column\n";
        \DB::update('UPDATE orders_api SET date=FROM_UNIXTIME(tmp_date)');
        echo now() . " drop tmp\n";
        Schema::table('orders_api', function (Blueprint $table) {
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
