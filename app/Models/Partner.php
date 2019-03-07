<?php

namespace App\Models;

class Partner extends Model
{
    public $fillable = ['name', 'key'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
