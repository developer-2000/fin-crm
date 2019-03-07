<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
class CompanyRepository
{
    public static function getCompany($id)
    {
        return DB::table('companies')->where('id', $id)->first();
    }
}