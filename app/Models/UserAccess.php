<?php

namespace App\Models;

class UserAccess extends Model
{
    protected $table = 'users_accesses';
    protected $fillable = ['entity_type', 'entity_id', 'company_id', 'role_id',
    'user_id', 'rank_id', 'access', 'rule_id', 'project_id', 'subproject_id'];

    public $timestamps = false;

    /**
     * Get all of the owning commentable models.
     */
    public function userAccessTable()
    {
        return $this->morphTo();
    }
}
