<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeProjectsAndStorehousesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(' ALTER TABLE `projects` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');


        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedInteger('parent_id')->nullable()->default(0)->index();
            //$table->timestamps();
            // MG: это сделали в параллельной, но более ранней миграции

            //$table->foreign('parent_id')->references('id')->on('projects');
        });

        $projects = DB::table('projects')->get();
        if ($projects) {
            $rows = [];
            foreach ($projects as $project) {
                $rows[] = [
                    'alias' => $project->alias,
                    'name' => $project->name,
                    'parent_id' => $project->id
                ];
            }
            DB::table('projects')->insert($rows);
        }

        // чтобы не баловаться к fk
        Schema::dropIfExists('storecurrents');
        Schema::dropIfExists('storehouses');

        Schema::create('storagecontents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('hold')->default(0);
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        echo '\nMigration "ChangeProjectsAndStorehousesTables" cannot reverted\n';
    }
}
