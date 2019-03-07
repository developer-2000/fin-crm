<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Documentation extends Model
{
    protected $dates = ['created_at', 'updated_at'];
    protected $fillable = ['name', 'text', 'category_id', 'priority'];
    protected $perPage = 10;
    /*
    * Relations
    */

    public function access()
    {
        return $this->morphMany(UserAccess::class, 'entity');
    }

    public function files()
    {
        return $this->morphToMany(File::class, 'entity', 'file_entity');
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    /*
    * Scope
    */
    public function scopeSort($query)
    {
        return $query->orderBy('id', 'desc');
    }

    public function scopeFilter($query, $data)
    {
        if ($data['name']) {
            $query->where('name', 'like', $data['name'].'%');
        }
        if ($data['category']) {
            $query->where('category_id', $data['category']);
        }

        return $query;
    }
}
