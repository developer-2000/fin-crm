<?php

namespace App\Models;

use \App\Models\User;

class Rank extends BaseModel
{
    protected $table = 'ranks';

    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
