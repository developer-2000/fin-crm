<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class File extends Model
{
    protected $dates = ['created_at', 'updated_at'];
    protected $fillable = ['name', 'path', 'option'];

    public function entities($modelName)
    {
        return $this->morphedByMany($modelName, 'entity', 'file_entity', 'file_id');
    }

}
