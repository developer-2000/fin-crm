<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedColumnsCodesInWefastCounterparties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wefast_counterparties', function (Blueprint $table) {
            $table->string('province_code')->index()->after('phone');
            $table->string('district_code')->index()->after('province_code');
            $table->string('ward_code')->index()->after('district_code');
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
