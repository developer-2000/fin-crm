<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use App\Models\TargetConfig;
use App\Models\ProcStatus;

class CodeStatus extends Model
{
    protected $table = 'codes_statuses';
    protected $fillable = ['integration_id', 'status_code', 'status', 'project_id', 'system_status_id'];

    public function getStatusAtDepartment(){
       return ProcStatus::senderStatuses()->systemStatuses()->where('action', 'at_department')->first();
    }

    public function targetConfig()
    {
        return $this->belongsTo(TargetConfig::class, 'id');
    }

    public function projectCodeStatus() {
        return $this->hasMany(ProjectCodeStatus::class, 'codes_statuses_id');
    }
}
