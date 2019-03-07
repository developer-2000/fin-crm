<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumnsInOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $columns = [
            'date_first_call',
            'call_max_count',
            'call_before',
            'status_ext',
            'owner_id',
            'client_id',
            'phone_error',
            'sub_id2',
            'sub_id3',
            'web_master_id',
            'flag_approve',
            'offer',
            'not_call_5',
            'not_call_15',
            'not_call_more',
            'api_flag',
            'up_sell',
            'up_sell_2',
            'cross_sell',
            'another_language',
            'tag_id',
            'updated_at',
            'created_at',
            'user_id_test',
            'not_call',
        ];

        Schema::table('orders', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column))
                {
                    $table->dropColumn($column);
                }
            }

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
