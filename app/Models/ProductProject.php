<?php

namespace App\Models;

class ProductProject  extends BaseModel
{
    protected $fillable = ['project_id', 'subproject_id', 'product_id', 'status'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    public function subProject()
    {
        return $this->belongsTo(Project::class,  'subproject_id');
    }
}
