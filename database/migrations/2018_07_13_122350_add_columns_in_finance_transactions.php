<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInFinanceTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_transaction', function (Blueprint $table) {
            $table->integer('sub_project_id')->index()->default(0)->after('company_id');
            $table->integer('pass_id')->index()->default(0)->after('offer_id');
            $table->decimal('balance_before')->default(0)->after('balance');
            $table->decimal('balance_after')->default(0)->after('balance_before');
            $table->decimal('order_price')->default(0)->after('balance_after');
            $table->decimal('cost_actual')->default(0)->after('order_price');
            $table->decimal('cost_return')->default(0)->after('cost_actual');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('finance_transactions', function (Blueprint $table) {
            //
        });
    }
}
