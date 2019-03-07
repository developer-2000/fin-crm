<?php

namespace App\Models\Api\Ninjaxpress;

use App\Models\TargetValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Project;
use App\Models\TargetConfig;

class NinjaxpressKey extends Model
{
    protected $fillable = [
        'target_id',
        'subproject_id',
        'active',
        'name',
        'phone',
        'country',
        'postcode',
        'address',
        'email',
        'password',
        'client_id',
        'client_secret',
        'access_token',
        'expires',
        'token_type',
        'expires_id',
        'size',
        'weight',
        'volume',
        'length',
        'width',
        'height',
    ];
    protected $table = 'ninjaxpress_keys';

    /**
     * get subproject
     * @return BelongsTo;
     */
    public function subProject()
    {
        return $this->belongsTo(Project::class, 'subproject_id');
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
