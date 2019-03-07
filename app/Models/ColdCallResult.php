<?php

namespace App\Models;

class ColdCallResult extends Model
{
    public $fillable = ['cold_call_list_id',  'count_status', 'call_status'];
    /**
     * Получить лист холодных продаж.
     */
    public function coldCallList()
    {
        return $this->belongsTo('App\Models\ColdCalList');
    }
}
