<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsStatusesOrdersOpened extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_opened', function (Blueprint $table) {
            $table->integer('proc_status')->after('user_id')->index();
            $table->integer('callback')->after('proc_status')->index()->default(0);
            $table->integer('target_status')->after('callback')->index()->default(0);
            $table->timestamp('target_status_time')->after('target_status')->index()->nullable();
            $table->integer('moderation_id')->after('target_status_time')->index()->default(0);
            $table->timestamp('moderation_time')->after('moderation_id')->index()->nullable();
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
