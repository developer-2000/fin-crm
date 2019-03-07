<?php

namespace App\Models;

class PlanLog extends Model
{
    /**
     * Получить план к логу.
     */
    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
    }

    /**
     * Получить компанию к логу
     */
    public function company()
    {
        return $this->hasMany('App\Models\Company');
    }
}
