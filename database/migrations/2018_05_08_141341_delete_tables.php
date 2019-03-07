<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            '_product',
            'account',
            'account_offers',
            'approve',
            'approve_operator',
            'audit',
            'audit_new',
            'audit_owners',
            'call_center_report',
            'call_processing',
            'call_result',
            'campaigns',
            'clients',
            'cold_call_lists_copy',
            'company_countries',
            'descriptions',
            'filters',
            'group_calls',
            'offers_cross_sell',
            'offers_img',
            'offers_target',
            'offers_target_final',
            'offers_target_final_option',
            'offers_target_option',
            'offers_up_sell',
            'offers_up_cross_sell',
            'order_offer_option',
            'postback',
            'report_operators',
            'settings_call_center',
            'targets',
            'target_final',
            'target_option',
            'test',
            'user_call_time',
            'user_calls',
            'user_order',
            'users_new',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::drop($table);
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
        //
    }
}
