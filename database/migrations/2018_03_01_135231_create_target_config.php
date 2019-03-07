<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTargetConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
            $table->string('alias')->index();
            $table->string('entity')->nullable()->index();
            $table->string('template')->index();
            $table->string('target_type')->index();
            $table->string('filter_geo')->nullable()->index();
            $table->integer('filter_offer')->nullable()->index();
            $table->string('filter_project')->nullable()->index();
            $table->string('tag_campaign')->nullable()->index();
            $table->string('tag_content')->nullable()->index();
            $table->string('tag_medium')->nullable()->index();
            $table->string('tag_source')->nullable()->index();
            $table->string('tag_term')->nullable()->index();
            $table->text('options');
            $table->string('active')->index();

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
