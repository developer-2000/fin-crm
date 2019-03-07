<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FileEntity extends Model
{
    protected $table = 'file_entity';
    protected $fillable = ['entity_type', 'entity_id', 'file_id'];
    public $timestamps = false;


    public function scopeFilter($query, $fileId, $entityId)
    {
        return $query->where('file_id', $fileId)->where('entity_id', $entityId);
    }
}
