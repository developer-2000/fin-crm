<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTargetFinalActionToProcStatuseses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proc_statuses', function (Blueprint $table) {
            $table->string('action_alias', 10)->index()->after('locked')->default(0);
            $table->string('action', 10)->index()->after('locked')->default(0);

            $table->string('target_final', 10)->index()->after('locked')->default(0);

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
