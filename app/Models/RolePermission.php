<?php

namespace App\Models;

class RolePermission extends Model
{
    protected $table = 'role_permissions';
    public $fillable = ['role_id', 'permission_id'];
    public $timestamps = false;
}
