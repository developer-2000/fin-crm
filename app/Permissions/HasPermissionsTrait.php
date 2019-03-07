<?php

namespace App\Permissions;

use App\Models\Permission;
use App\Models\Role;

trait HasPermissionsTrait
{

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles');

    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'users_permissions');

    }

    public function hasRole( ... $roles )
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('name', $role)) {
                return true;
            }
        }
        return false;
    }

    public function hasPermissionTo( $permission )
    {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermissions($permission);
    }

    protected function hasPermissions( $permission )
    {
        return (bool)$this->permissions->where('name', $permission->name)->count();
    }

    public function hasPermissionThroughRole( $permission )
    {
            foreach ($permission->roles as $role) {
                if ($this->roles->contains($role)) {
                    return true;
                }
            }

        return false;
    }

    protected function getAllPermissions( array $permissions )
    {
        return Permission::whereIn('name', $permissions)->get();
    }
}