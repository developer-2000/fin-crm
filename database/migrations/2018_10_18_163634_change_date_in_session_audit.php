<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateInSessionAudit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('session_audit', function (Blueprint $table) {
            $table->dropColumn('time');
        });

        Schema::table('session_audit', function (Blueprint $table) {
            $table->index('datetime');
        });
        \DB::statement('ALTER TABLE session_audit MODIFY COLUMN datetime
          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
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
