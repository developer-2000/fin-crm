<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldsInUsersAccesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('users_accesses', function (Blueprint $table) {
          $table->renameColumn('entity', 'entity_type');
          $table->boolean('access')->change();
          $table->integer('company_id')->nullable()->default(NULL)->change();
          $table->integer('role_id')->nullable()->default(NULL)->change();
          $table->integer('rank_id')->nullable()->default(NULL)->change();
          $table->integer('user_id')->nullable()->default(NULL)->change();
          $table->integer('project_id')->nullable()->default(NULL);
          $table->integer('subproject_id')->nullable()->default(NULL);
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
