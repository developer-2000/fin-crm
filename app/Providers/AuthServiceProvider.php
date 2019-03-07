<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupCollection;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     * @param GateContract $gate
     */
    public function boot(GateContract $gate) {
        $this->registerPolicies();

//        foreach($this->getPermissions() as $permission) {
//            $gate->define($permission->name, function(User $user) use ($permission) {
//                return $user->hasPermission($permission->name);
//            });
//        };
    }

    /**
     * @return Permission[]|Builder[]|Collection|SupCollection
     */
    protected function getPermissions() {
        return Permission::with('roles')->get();
    }
}
