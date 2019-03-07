<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdProduct extends Model
{
    protected $table = 'ord_products';

    protected $guarded = [];

//    ====================================================================
//    belongsTo     ======================================================
//    ====================================================================

    public function ordorder()
    {
        return $this->belongsTo(OrdOrder::class, 'order_id', 'id');
    }



}
