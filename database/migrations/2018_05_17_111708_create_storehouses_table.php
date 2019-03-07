<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateStorehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // склады
        Schema::create('storehouses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id')->index();
            $table->string('alias')->index();
            $table->string('name')->index();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });

        // делаю по одному складу на каждый проект с аналогичными alias и name
        $projects = DB::table('projects')->orderBy('id', 'asc')->get();
        if ($projects) {
            $rows = [];
            foreach ($projects as $project) {
                $rows[] = [
                    'alias' => $project->alias,
                    'name' => $project->name,
                    'project_id' => $project->id
                ];
            }
            DB::table('storehouses')->insert($rows);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storehouses');
    }
}
