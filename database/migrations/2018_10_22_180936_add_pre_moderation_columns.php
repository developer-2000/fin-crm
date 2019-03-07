<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreModerationColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('pre_moderation_uid')->default(0)->index()->after('moderation_time');
            $table->integer('pre_moderation_type')->default(0)->index()->after('pre_moderation_uid');
            $table->integer('pre_moderation_time')->default(0)->index()->after('pre_moderation_type');
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
