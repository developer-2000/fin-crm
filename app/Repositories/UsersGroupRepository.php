<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UsersGroupRepository
{
    public static function operatorsGroupSorted()
    {
        return DB::table('users_group')->orderBy('name')->get();
    }
}