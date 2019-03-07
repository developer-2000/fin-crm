<?php

namespace App\Repositories;


class RankRepository
{
    public static function findByName($term,$rolesIds ){

        $ranks = \DB::table('ranks')
            ->select('id', 'name')
            ->where('name', 'LIKE', '%' . $term . '%');
        if(!empty($rolesIds)){
            $ranks->whereIn('role_id',  $rolesIds);
        }

        return $ranks->get();
    }
}
