<?php
namespace App\Repositories;
use Illuminate\Support\Facades\DB;

class RoleRepository
{
       public static function findByName($term, $companies = [], $projects = [])
       {
           $query = DB::table('role AS r')
                   ->select(DB::raw('DISTINCT(r.id) AS id'), 'r.name')
                   ->leftJoin('users AS u', 'r.id', '=', 'u.role_id');

                   if($companies) $query->whereIn('u.company_id', $companies);

                   if($projects) $query->whereIn('u.project_id', $companies);

                   return $query->get();
       }
   }
