<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateInReportTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo now() . " report_time, create tmp\n";
        if (!Schema::hasColumn('report_time', 'tmp_date')) {
            Schema::table('report_time', function (Blueprint $table) {
                $table->integer('tmp_date')->nullable();
            });
        }
        echo now() . " update tmp\n";
        \DB::update('UPDATE report_time SET tmp_date=date');
        echo now() . " drop column\n";
        Schema::table('report_time', function (Blueprint $table) {
            $table->dropPrimary(['user_id', 'date']);
            $table->dropColumn('date');
        });
        echo now() . " create column\n";
        Schema::table('report_time', function (Blueprint $table) {
            $table->timestamp('date')->useCurrent()->index();
        });
        echo now() . " update column\n";
        \DB::update('UPDATE report_time SET date=FROM_UNIXTIME(tmp_date)');
        echo now() . " drop tmp\n";
        Schema::table('report_time', function (Blueprint $table) {
            $table->dropColumn('tmp_date');
        });
        Schema::table('report_time', function (Blueprint $table) {
            $table->primary(['user_id', 'date']);
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
