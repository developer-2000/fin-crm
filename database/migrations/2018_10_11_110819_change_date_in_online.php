<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateInOnline extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo now() . " online, drop fields\n";
        Schema::table('online', function (Blueprint $table) {
            $table->dropColumn('date_start');
            $table->dropColumn('date_end');
        });
        echo now() . " create tmp\n";
        Schema::table('online', function (Blueprint $table) {
            $table->timestamp('date_start')->useCurrent()->index();
            $table->timestamp('date_end')->nullable()->index();
        });
        echo now() . " update date\n";
        \DB::update('UPDATE online SET date_start=date_start_datetime, date_end=date_end_datetime');
        echo now() . " drop tmp\n";
        Schema::table('online', function (Blueprint $table) {
            $table->dropColumn('date_start_datetime');
            $table->dropColumn('date_end_datetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('online', function (Blueprint $table) {
            //
        });
    }
}
