<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class Source extends BaseModel
{
    protected $table = 'source'; 
    
    function getAllSource() 
    {
        return DB::table('projects')->get();
    }

}
