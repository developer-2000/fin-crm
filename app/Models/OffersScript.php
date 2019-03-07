<?php

namespace App\Models;

class OffersScript extends Model
{
    protected $fillable = ['offer_id', 'script_id'];

    public function scripts(){
        return $this->belongsTo(Script::class);
    }
    public function offers(){
        return $this->belongsTo(Offer::class);
    }
}
