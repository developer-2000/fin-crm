<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateFinancePayouts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo now() . " fin_payouts, create tmp\n";
        Schema::table('finance_payouts', function (Blueprint $table) {
            $table->integer('tmp_start')->nullable();
            $table->integer('tmp_end')->nullable();
            $table->integer('tmp_created')->nullable();
        });
        echo now() . " update tmp date\n";
        \DB::update('UPDATE finance_payouts SET tmp_start=period_start,tmp_end=period_end,tmp_created=time_created ');
        echo now() . " drop columns\n";
        Schema::table('finance_payouts', function (Blueprint $table) {
            $table->dropColumn('period_start');
            $table->dropColumn('period_end');
            $table->dropColumn('time_created');
        });
        echo now() . " create columns\n";
        Schema::table('finance_payouts', function (Blueprint $table) {
            $table->timestamp('period_start')->nullable()->index();
            $table->timestamp('period_end')->nullable()->index();
            $table->timestamp('time_created')->useCurrent()->index();
        });
        echo now() . " update columns\n";
        \DB::update('UPDATE finance_payouts SET period_start=FROM_UNIXTIME(tmp_start), period_end=FROM_UNIXTIME(tmp_end), time_created=FROM_UNIXTIME(tmp_created)');
        echo now() . " drop tmp\n";
        Schema::table('finance_payouts', function (Blueprint $table) {
            $table->dropColumn('tmp_start');
            $table->dropColumn('tmp_end');
            $table->dropColumn('tmp_created');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_payouts', function (Blueprint $table) {
            //
        });
    }
}
