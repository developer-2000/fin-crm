<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateInUsersTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo now() . " users_time, drop times\n";
        Schema::table('users_time', function (Blueprint $table) {
            $table->dropColumn('time_start');
            $table->dropColumn('time_end');
        });
        Schema::table('users_time', function (Blueprint $table) {
            $table->index('datetime_end');
            $table->index('datetime_start');
        });
        echo now() . " users_time, update columns\n";
        \DB::statement('ALTER TABLE users_time MODIFY COLUMN datetime_end
          TIMESTAMP NULL DEFAULT NULL');
        \DB::statement('ALTER TABLE users_time MODIFY COLUMN datetime_start
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
