<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateInOrdersOpened extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo now() . " orders_opened, create tmp\n";
        Schema::table('orders_opened', function (Blueprint $table) {
            $table->integer('tmp_date_opened')->nullable();
            $table->integer('tmp_date_closed')->nullable();
        });
        echo now() . " update tmp\n";
        \DB::update('UPDATE orders_opened SET tmp_date_opened=date_opening, tmp_date_closed=date_closed');
        echo now() . " drop column\n";
        Schema::table('orders_opened', function (Blueprint $table) {
            $table->dropColumn('date_opening');
            $table->dropColumn('date_closed');
        });
        echo now() . " create column\n";
        Schema::table('orders_opened', function (Blueprint $table) {
            $table->timestamp('date_opening')->useCurrent()->index();
            $table->timestamp('date_closed')->nullable()->default(null)->index();
        });
        echo now() . " update column\n";
        \DB::update('UPDATE orders_opened SET 
            date_opening= CASE WHEN tmp_date_opened > 0 THEN FROM_UNIXTIME(tmp_date_opened) ELSE NOW() END, 
            date_closed= CASE WHEN tmp_date_closed > 0 THEN FROM_UNIXTIME(tmp_date_closed) ELSE null END
            ');
        echo now() . " drop tmp\n";
        Schema::table('orders_opened', function (Blueprint $table) {
            $table->dropColumn('tmp_date_opened');
            $table->dropColumn('tmp_date_closed');
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
