<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovingProductPart extends Model
{
    protected $table = 'moving_product_parts';

    public $timestamps = false;

    protected $fillable = ['mp_id', 'amount', 'status', 'user_id'];

    protected $dates = [
        'created_at'
    ];

    const STATUS_ARRIVED = 1;
    const STATUS_SHORTFALL = 2;

    public static $statuses = [
        self::STATUS_ARRIVED => 'arrived',
        self::STATUS_SHORTFALL => 'shortfall'
    ];

    public static function langStatuses() {
        $statuses = [];
        foreach (static::$statuses as $key => $value) {
            $statuses[$key] = $value;
        }
        return $statuses;
    }

    /**
     * @return BelongsTo
     */
    public function moving_product() {
        return $this->belongsTo(MovingProduct::class, 'mp_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
