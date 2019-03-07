<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateInTargetValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo now() . " target_values, create tmp\n";
        if (!Schema::hasColumn('target_values', 'tmp_time_created')) {
            Schema::table('target_values', function (Blueprint $table) {
                $table->integer('tmp_time_created')->nullable();
            });
        }
        echo now() . " update tmp\n";
        \DB::update('UPDATE target_values SET tmp_time_created=time_created');
        echo now() . " drop column\n";
        Schema::table('target_values', function (Blueprint $table) {
            $table->dropColumn('time_created');
        });
        echo now() . " create column\n";
        Schema::table('target_values', function (Blueprint $table) {
            $table->timestamp('time_created')->useCurrent()->index();
        });
        echo now() . " update column\n";
        \DB::update('UPDATE target_values SET time_created=FROM_UNIXTIME(tmp_time_created)');
        echo now() . " drop tmp\n";
        Schema::table('target_values', function (Blueprint $table) {
            $table->dropColumn('tmp_time_created');
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
