<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStorehouseIdInStorehouses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // MG: без надобности, т.к. эта таблица удалена
        /*Schema::table('storehouses', function (Blueprint $table) {
            $table->integer('storehouse_id')->index()->after('project_id');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('storehouse', function (Blueprint $table) {
            //
        });*/
    }
}
