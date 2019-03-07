<?php

use Illuminate\Database\Seeder;

class UpdateProjects extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $projects = \App\Models\Project::where([['parent_id', 0],['project_id', 0]])->get();
        foreach ($projects as $project) {
            $project->project_id = $project->id;
            $project->save();
        }
    }
}
