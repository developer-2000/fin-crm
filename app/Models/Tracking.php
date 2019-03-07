<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tracking extends Model
{
    protected $table = 'tracking';
    protected $fillable = [
        'status_code',
        'status',
        'order_id',
        'track',
        'target_id',
        'comment',
        'delivery_info',
        'created_at',
        'updated_at'
    ];

    /**
     * get TargetValue
     * @return BelongsTo
     */
    public function targetValue()
    {
        return $this->belongsTo(TargetValue::class, 'order_id', 'order_id');
    }
}
