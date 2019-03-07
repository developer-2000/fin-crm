<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{

    const TYPE_MANUAL = 'manual';

    protected $fillable = ['type','order_id', 'user_id', 'order_log_id', 'comment'];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function log(){
        return $this->belongsTo(OrdersLog::class, 'order_log_id');
    }
}
