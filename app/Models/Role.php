<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Role extends Model
{
    protected $table = 'role';

    public $fillable = ['name','project_id'];
    public $timestamps = false;

    const ADMIN_NAME = 'Администратор';

    public function ranks()
    {
        return $this->hasMany(Rank::class);
    }

    /**
     * get permissions
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * get users
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function getAllRoles($company = false)
    {
        $result = DB::table($this->table);
        if ($company){
            $result = $result->where('company', 1);
        }
        return collect($result->get())->keyBy('id');
    }
}
