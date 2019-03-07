<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    protected $fillable = ['title', 'body', 'author_id', 'category_id',
     'active', 'count_view', 'required', 'priority', 'publish_at'];

    protected $dates = ['publish_at'];
    protected $perPage = 10;


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    /**
     * get author
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo;
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }
    /**
     * get author
     * @return \Illuminate\Database\Eloquent\Relations\HasMany;
     */
    public function postsUsers()
    {
        return $this->hasMany(PostUser::class);
    }

    /*
    * Scope
    */
    public function scopeFilter($query, $data)
    {
        if ($data['title']) {
            $query->where('title', 'like', $data['title'] . '%');
        }
        if ($data['category']) {
            $query->where('category_id', $data['category']);
        }

        return $query;
    }

    public function scopePublic($query, $date)
    {
        $query->where('active', 1)
            ->where('publish_at', '<=', $date)
            ->orderBy('publish_at', 'desc');

        return $query;
    }
}
