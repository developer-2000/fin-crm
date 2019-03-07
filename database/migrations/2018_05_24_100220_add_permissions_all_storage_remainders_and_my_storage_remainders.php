<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionsAllStorageRemaindersAndMyStorageRemainders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insert([
            ['name' => 'all_storage_remainders', 'alias' => 'Все остатки системы', 'group' => null],
            ['name' => 'my_storage_remainders', 'alias' => 'Остатки одного проекта', 'group' => 'menu'],
        ]);
        DB::table('permissions')->where(['name' => 'all_storages'])->update(['group' => NULL]);
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
