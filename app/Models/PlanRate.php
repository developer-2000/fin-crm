<?php

namespace App\Models;

class PlanRate extends Model
{
    protected $fillable = ['data'];
    public $timestamps = false;

    /*get plan-rates-offers*/
    public function planRatesOffers()
    {
        return $this->hasMany(PlanRateOffer::class);
    }
}
