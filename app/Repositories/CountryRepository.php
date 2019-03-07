<?php

namespace App\Repositories;

use App\Models\Country as Country;

class CountryRepository
{
    public static function countriesSortByName()
    {
        return Country::all()->sortBy('name');
    }
}