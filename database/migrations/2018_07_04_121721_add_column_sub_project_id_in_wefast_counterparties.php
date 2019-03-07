<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSubProjectIdInWefastCounterparties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wefast_counterparties', function (Blueprint $table) {
            $table->integer('sub_project_id')->index()->after('key_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wefast_counterparties', function (Blueprint $table) {
            //
        });
    }
}
