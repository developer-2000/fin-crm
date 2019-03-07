<?php

namespace App\Repositories;

use App\Models\Offer;

class OfferRepository
{
    public static function offersSortByName(){
        return Offer::all()->sortBy('name');
    }
}