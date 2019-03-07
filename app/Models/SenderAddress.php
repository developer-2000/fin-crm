<?php

namespace App\Models;

use App\Models\Api\NovaposhtaKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SenderAddress extends Model
{
    protected $fillable = ['ref', 'name', 'integration_key_id'];
    protected $table = 'sender_addresses';

    /**
     * get integrationKey
     * @return BelongsTo;
     */
    public function integrationKey()
    {
        return $this->belongsTo(NovaposhtaKey::class);
    }
}
