<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProcStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proc_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->index();
            $table->string('name')->index();
            $table->string('type', 50)->index();
            $table->integer('parent_id')->index()->default(0);
            $table->tinyInteger('locked')->index();
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
        Schema::dropIfExists('proc_statuses');
    }
}
