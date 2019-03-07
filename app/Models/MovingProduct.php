<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MovingProduct extends Model
{
    protected $table = 'moving_product';

    public $timestamps = false;

    protected $fillable = ['product_id', 'amount', 'moving_id'];

    /**
     * @return BelongsTo
     */
    // продукт в движении
    public function product() {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function moving() {
        return $this->belongsTo(Moving::class, 'moving_id', 'id');
    }


    /**
     * @return HasMany
     */
    // инфа об идущем частями движении (т.е. о частях продукта в движении)
    public function parts() {
        return $this->hasMany(MovingProductPart::class, 'mp_id', 'id');
    }
}
