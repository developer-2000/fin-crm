<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class ProjectCodeStatus extends Model
{
    protected $table = 'project_codes_statuses';
    protected $fillable = ['codes_statuses_id', 'proc_status_id'];
    public $timestamps = false;

    public function codeStatus()
    {
        return $this->belongsTo(CodeStatus::class, 'id');
    }
}
