<?php

namespace App\Models;

class PlanRateOffer extends Model
{
    protected $fillable = ['offer_id', 'plan_rate_id'];
    protected $table = 'plan_rates_offers';
    public $timestamps = false;
    protected $primaryKey = 'offer_id';

    /*get planOffer*/
    public function planRate(){
        return $this->belongsTo(PlanRate::class);
    }
    /*get Offer*/
    public function offer(){
        return $this->belongsTo(Offer::class);
    }
}
