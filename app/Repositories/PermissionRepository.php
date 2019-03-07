<?php

namespace App\Repositories;


use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PermissionRepository
{
    public static function getPermissionsOneUser(User $user)
    {
        return DB::table(Permission::tableName() . ' AS p')
            ->select('p.name')
            ->leftJoin('role_permissions AS rp', 'rp.permission_id', '=', 'p.id')
            ->leftJoin('users_permissions AS up', 'up.permission_id', '=', 'p.id')
            ->whereIn('rp.role_id', $user->roles->pluck('id')->toArray())
            ->orWhere('up.user_id', $user->id)
            ->get();
    }

    public function setRolePermission($pid, $rid, $status)
    {
        if ($status) {
            return $this->insertRolePermission($rid, $pid);
        } else {
            return $this->deleteRolePermission($rid, $pid);
        }
    }

    public function insertRolePermission($roleId, $permissionId)
    {
        return DB::table('role_permissions')
            ->insert([
                'role_id'       => $roleId,
                'permission_id' => $permissionId
            ]);
    }

    public function deleteRolePermission($roleId, $permissionId)
    {
        return DB::table('role_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->delete();
    }
}