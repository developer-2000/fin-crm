<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCodesStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codes_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('integration_id')->index(); // target_config_id
            $table->string('status_code')->index(); //integration post code
            $table->text('status'); //integration post code name
            $table->integer('system_status_id')->index();
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
        //
    }
}
