<?php

namespace App\Models\Api;

use App\Models\TargetValue;
use App\Models\ViettelSender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Project;
use App\Models\TargetConfig;

class ViettelKey extends Model
{
    protected $fillable = ['target_id', 'subproject_id', 'name', 'active', 'user_name',
                           'user_id', 'role', 'from_source', 'token_key', 'email'];
    protected $table = 'viettel_keys';

    /**
     * get subproject
     * @return BelongsTo;
     */
    public function subProject()
    {
        return $this->belongsTo(Project::class, 'subproject_id');
    }

    public  function senders(){
        return $this->hasMany(ViettelSender::class);
    }

    public function target()
    {
        return $this->belongsTo(TargetConfig::class, 'target_id');
    }
    public function targetValue()
    {
        return $this->hasMany(TargetValue::class, 'sender_id');
    }
}
