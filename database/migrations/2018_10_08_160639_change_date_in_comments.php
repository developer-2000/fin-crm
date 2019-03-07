<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateInComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo now() . " comments, create tmp\n";
        if (!Schema::hasColumn('comments', 'tmp_date')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->integer('tmp_date')->nullable();
            });
        }
        echo now() . " update tmp\n";
        \DB::update('UPDATE comments SET tmp_date=date');
        echo now() . " drop column\n";
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('date');
        });
        echo now() . " create column\n";
        Schema::table('comments', function (Blueprint $table) {
            $table->timestamp('date')->useCurrent()->index();
        });
        echo now() . " update column\n";
        \DB::update('UPDATE comments SET date=FROM_UNIXTIME(tmp_date)');
        echo now() . " drop tmp\n";
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('tmp_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            //
        });
    }
}
