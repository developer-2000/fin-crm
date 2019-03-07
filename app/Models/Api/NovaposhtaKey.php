<?php

namespace App\Models\Api;

use App\Models\SenderAddress;
use App\Models\TargetConfig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Project;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Order;

class NovaposhtaKey extends Model
{
    protected $fillable = ['target_id', 'key', 'name', 'exp_key_date', 'active', 'sender_id', 'contacts', 'weight', 'size',
                           'description', 'subproject_id', 'integration_id', 'sender_address_id'];

    /**
     * get integration
     * @return BelongsTo;
     */
    public function integration()
    {
        return $this->belongsTo(TargetConfig::class);
    }

    /**
     * get subproject
     * @return BelongsTo;
     */
    public function subProject()
    {
        return $this->belongsTo(Project::class, 'subproject_id');
    }

    /**
     * get senderAddresses
     * @return HasMany;
     */
    public function senderAddresses()
    {
        return $this->hasMany(SenderAddress::class);
    }
}
