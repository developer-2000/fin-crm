<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStoragesPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->whereIn('name', [
            'all_storage_remainders',
            'my_storage_remainders',
            'my_storages',
            'my_transactions'
        ])->delete();
        DB::table('permissions')
            ->where(['name' => 'all_storages'])
            ->update(['group' => 'menu', 'name' => 'storages', 'alias' => 'Склады']);
        DB::table('permissions')
            ->where(['name' => 'my_movings'])
            ->update(['group' => 'menu', 'name' => 'movings', 'alias' => 'Движения']);
        DB::table('permissions')
            ->where(['name' => 'all_transactions'])
            ->update(['group' => 'menu', 'name' => 'transactions', 'alias' => 'Транзакции']);
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
