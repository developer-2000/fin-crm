<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateInCallProgressLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo now() . " call_progress_log, create tmp\n";
        Schema::table('call_progress_log', function (Blueprint $table) {
            $table->integer('tmp_start')->nullable();
            $table->integer('tmp_date')->nullable();
        });
        echo now() . " update tmp date\n";
        \DB::update('UPDATE call_progress_log SET tmp_start=start_time,tmp_date=date');
        echo now() . " drop column\n";
        Schema::table('call_progress_log', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->dropColumn('start_time');
        });
        echo now() . " create column\n";
        Schema::table('call_progress_log', function (Blueprint $table) {
            $table->timestamp('start_time')->after('trunk')->nullable()->index();
            $table->timestamp('date')->after('start_time')->useCurrent()->index();
        });
        echo now() . " update date\n";
        \DB::update('UPDATE call_progress_log SET start_time=FROM_UNIXTIME(tmp_start) ,date=FROM_UNIXTIME(tmp_date)');
        echo now() . " drop tmp\n";
        Schema::table('call_progress_log', function (Blueprint $table) {
            $table->dropColumn('tmp_start');
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
        Schema::table('call_progress_log', function (Blueprint $table) {
            //
        });
    }
}
