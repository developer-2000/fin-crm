<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
// Мы будем использовать директиву Laravel «can», чтобы проверить, есть ли у Пользователя Разрешение. и вместо использования $ user-> hasPermissionTo (),
// мы будем использовать $ user-> can (). Для этого нам нужно создать новый PermissionsServiceProvider для авторизации.
        Permission::with('roles')->get()->map(function ( $permission ) {
            Gate::define($permission->name, function ( $user ) use ( $permission ) {
                return $user->hasPermissionTo($permission);
            });
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
