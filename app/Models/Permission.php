<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Permission extends Model
{
    protected $table = 'permissions';

    /**
     * get role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * get users
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_permissions');
    }

    //todo переместить в репозиторий
    public function userAccess($permissionName)
    {
        $query = DB::table($this->table . ' AS p')
            ->leftJoin('role_permissions AS rp', 'p.id', '=', 'rp.permission_id')
            ->where('rp.role_id', auth()->user()->role_id);
        if (is_array($permissionName)) {
            $query = $query->whereIn('p.name', $permissionName)
                ->first();
        } else {
            $query = $query->where('p.name', $permissionName)
                ->first();
        }
        return $query;

    }
}
