<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateInOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo now() . " orders\n";
        $tmp = '_tmp';
        $columns = [
            'time_created',
            'time_modified',
            'time_changed',
            'proc_time',
            'proc_callback_time',
            'moderation_time',
            'pre_moderation_time'
        ];

        echo now() . " create tmp fields\n";
        Schema::table('orders', function (Blueprint $table) use ($columns, $tmp) {
            foreach ($columns as $column) {
                if (!Schema::hasColumn('orders', $column . $tmp)) {
                    $table->integer($column . $tmp)->nullable();
                }
            }
        });
        echo now() . " copy data\n";
        $query = 'UPDATE orders SET ';
        foreach ($columns as $column) {
            $query .= $column . $tmp . '=' . $column;
            if ($column != $columns[count($columns) - 1]) {
                $query .= ',';
            }
        }
        \DB::update($query);

        echo now() . " drop columns\n";
        Schema::table('orders', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                $table->dropColumn($column);
            }
            //$table->dropColumn('old');
        });
        echo now() . " create fields\n";
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('time_created')->after('price_products')->useCurrent()->index();
            $table->timestamp('time_changed')->after('time_created')->index();
            $table->timestamp('time_modified')->after('time_changed')->useCurrent()->index();
            $table->timestamp('proc_time')->after('proc_call_id')->useCurrent()->index();
            $table->timestamp('proc_callback_time')->after('proc_time')->default(null)->nullable()->index();
            $table->timestamp('moderation_time')->after('moderation_id')->default(null)->nullable()->index();
            $table->timestamp('pre_moderation_time')->after('pre_moderation_type')->default(null)->nullable()->index();
        });
        echo now() . " update fields\n";

        \DB::update('UPDATE orders SET
            time_created= CASE WHEN time_created_tmp > 0 THEN FROM_UNIXTIME(time_created_tmp) ELSE NOW() END,
            time_modified=CASE WHEN time_modified_tmp > 0 THEN FROM_UNIXTIME(time_modified_tmp) ELSE NOW() END,
            time_changed=CASE WHEN time_changed_tmp > 0 THEN FROM_UNIXTIME(time_changed_tmp) ELSE NOW() END,
            proc_time=CASE WHEN proc_time_tmp > 0 THEN FROM_UNIXTIME(proc_time_tmp) ELSE NOW() END,
            proc_callback_time=CASE WHEN proc_callback_time_tmp > 0 THEN FROM_UNIXTIME(proc_callback_time_tmp) ELSE NULL END ,
            moderation_time=CASE WHEN moderation_time_tmp > 0 THEN FROM_UNIXTIME(moderation_time_tmp) ELSE NULL END,
            pre_moderation_time=CASE WHEN pre_moderation_time > 0 THEN FROM_UNIXTIME(pre_moderation_time_tmp) ELSE NULL END ');
        echo now() . " drop tmp\n";
        Schema::table('orders', function (Blueprint $table) use ($columns, $tmp) {
            foreach ($columns as $column) {
                $table->dropColumn($column.$tmp);
            }
        });

        echo now() . " updated time statuses\n";
        $columnStatuses = [
            'sent',
            'at_department',
            'received',
            'returned',
            'paid_up',
            'refused',
            'status_updated'
        ];
        foreach ($columnStatuses as $columnStatus) {
            \DB::statement('ALTER TABLE orders MODIFY COLUMN time_' . $columnStatus . ' TIMESTAMP NULL DEFAULT NULL');
        }

        echo now() . "\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
