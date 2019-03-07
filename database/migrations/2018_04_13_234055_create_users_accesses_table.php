<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_accesses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rule_id')->index()->nullable()->default(0);
            $table->string('entity')->index();
            $table->integer('entity_id')->index(); // id of post, ticket, test
            $table->integer('company_id')->index()->nullable()->default(0);
            $table->integer('role_id')->index()->nullable()->default(0);
            $table->integer('rank_id')->index()->nullable()->default(0);
            $table->integer('user_id')->index()->nullable()->default(0);
            $table->string('access')->index()->nullable();//on or off
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users_accesses');
    }
}
