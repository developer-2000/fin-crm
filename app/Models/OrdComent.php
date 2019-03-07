<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdComent extends Model
{
    protected $table = 'ord_coments';

    protected $guarded = [];

//    ====================================================================
//    belongsTo     ======================================================
//    ====================================================================

    public function ordorder()
    {
        return $this->belongsTo(OrdOrder::class, 'order_id', 'id');
    }
}
