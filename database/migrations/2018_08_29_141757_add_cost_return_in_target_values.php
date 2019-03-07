<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCostReturnInTargetValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('target_values', function (Blueprint $table) {
            $table->decimal('cost_return', 30, 2)->default(0);
            $table->decimal('cost_actual', 30, 2)->default(0)->change();
            $table->decimal('cost', 30, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('target_values', function (Blueprint $table) {
            //
        });
    }
}
