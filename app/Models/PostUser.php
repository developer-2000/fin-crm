<?php

namespace App\Models;

use \App\Models\User;

class PostUser extends Model
{
    protected $table = 'posts_users';
    protected $fillable = ['user_id', 'post_id', 'viewed', 'familiar'];
    public $timestamps = false;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function posts()
    {
        return $this->belongsTo(Post::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
