<?php

namespace App\Http\Middleware;

use App\Models\Api\Measoft\MeasoftSender;
use App\Models\Permission;
use App\Models\SessionAudit;
use App\Repositories\PermissionRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/* CHECKED */

class CheckPermissions
{
    public static $permissions = [];

    function handle( $request, Closure $next, $guard = null )
    {
        if (auth()->user()) {

            $this->setUserTimeZone();
//            if (auth()->user()->role_id != 17) {
//
//                return redirect()->route('index');
//            }

            app()->setLocale(auth()->user()->language);

            $this->setPermissions();

            /**лог пользователя*/
            $this->addEventUser(auth()->user()->id);

            return $next($request);
        }
        return redirect()->route('index');
    }

    protected function addEventUser( $userId )
    {
        if (Route::currentRouteName() != 'online') {
            $sessionAudit = new SessionAudit();
            $data = [
                'user_id' => $userId,
                'session_id' => session_id(),
                'datetime' => now(),
                'route' => Route::currentRouteName()
            ];
            $sessionAudit->setEvent($data);
        }
    }

    protected function setPermissions()
    {
        auth()->user()->permissions = PermissionRepository::getPermissionsOneUser(auth()->user())
            ->keyBy('name');
        self::$permissions = auth()->user()->permissions;
    }

    protected function setUserTimeZone()
    {
        try {
            $hourDiff = Carbon::now(Auth::user()->time_zone)->format('P');
            date_default_timezone_set(Auth::user()->time_zone);
            //утсановка timezone для пользователя
            \DB::update('SET time_zone = ?', [$hourDiff]);
        } catch (\Exception $exception) {
        }
    }
}