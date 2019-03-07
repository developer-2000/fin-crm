<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColdCallFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cold_call_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_name');
            $table->string('status');
            $table->string('comment');

            $table->string('geo');
            $table->integer('company_id');
            $table->integer('campaign_id');

            $table->index('status');
            $table->index('geo');
            $table->index('company_id');
            $table->index('campaign_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cold_call_files');
    }
}
