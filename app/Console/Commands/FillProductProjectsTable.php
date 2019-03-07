<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductProject;
use App\Models\Project;
use App\Models\StorageContent;
use Illuminate\Console\Command;

class FillProductProjectsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill_product_projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill product projects';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $storageItems = StorageContent::all();
        foreach ($storageItems as $storageItem) {
            ProductProject::updateOrCreate([
                'project_id'    => Project::find($storageItem->project_id)->parent_id,
                'subproject_id' => $storageItem->project_id,
                'product_id'    => $storageItem->product_id,
                'status'        => 1
            ]);
        }
    }
}
