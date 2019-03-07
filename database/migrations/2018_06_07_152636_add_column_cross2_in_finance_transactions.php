<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCross2InFinanceTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('finance_transaction', function (Blueprint $table) {
            $table->integer('cross2')->index()->default(0)->after('count_cross');
            $table->integer('count_cross2')->index()->default(0)->after('cross2');
        });

        $companies = \App\Models\Company::all();

        if ($companies->isNotEmpty()) {
            foreach ($companies as $company) {
                if ($company->type == 'lead') {
                    $prices = json_decode($company->prices, true);
                    $prices['global']['cross_sell_2'] = 0;
                    $company->prices = json_encode($prices);
                }
                if ($company->billing_type == 'lead') {
                    $prices = json_decode($company->billing, true);
                    $prices['global']['cross_sell_2'] = 0;
                    $company->billing = json_encode($prices);
                }
                $company->save();
            }
        }
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
