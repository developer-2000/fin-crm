<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateFinanceTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo now() . " fin_transaction, create tmp\n";
        Schema::table('finance_transaction', function (Blueprint $table) {
            $table->integer('tmp_created')->nullable();
            $table->integer('tmp_modified')->nullable();
        });

        echo now() . " update tmp data\n";
        \DB::update('UPDATE finance_transaction SET tmp_created=time_created,tmp_modified=time_modified');

        echo now() . " drop columns\n";
        Schema::table('finance_transaction', function (Blueprint $table) {
            $table->dropColumn('time_created');
            $table->dropColumn('time_modified');
        });

        echo now() . " create columns\n";
        Schema::table('finance_transaction', function (Blueprint $table) {
            $table->timestamp('time_created')->useCurrent()->index();
            $table->timestamp('time_modified')->useCurrent()->index();
        });

        echo now() . " update columns data\n";
        \DB::update('UPDATE finance_transaction SET time_created=FROM_UNIXTIME(tmp_created), time_modified=FROM_UNIXTIME(tmp_modified)');

        echo now(). " drop tmp\n";
        Schema::table('finance_transaction', function (Blueprint $table) {
            $table->dropColumn('tmp_created');
            $table->dropColumn('tmp_modified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_transaction', function (Blueprint $table) {
            //
        });
    }
}
