<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckPermissions;
use App\Models\Projects_new;
use App\Repositories\TranslationRepository;
use App\Services\Menu\MenuService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

abstract class BaseController extends Controller
{
//AuthorizesResources
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  //  protected $permissions;
    /**
     * All of the current user's projects.
     */
    protected $projects;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ( Auth::user()) {
                $menu = $this->createMenu();

                $this->permissions = CheckPermissions::$permissions;
                View::share('permissions', $this->permissions);
                View::share('menu', $menu);
                View::share('languages', TranslationRepository::getLocales());
                return $next($request);
            }
        });
    }

    public function getFilterUrl($data)
    {
        $url = '';
        foreach ($data as $filterKey => $filterValue) {
            if ($filterValue) {
                if (is_array($filterValue)) {
                    if(!empty($filterValue[0])){
                        $filterValue = implode(',', $filterValue);
                    }else{
                        continue;
                    }
                }
                $url .= '&' . $filterKey . '=' . $filterValue;
            }
        }
        return $url ? '?' . substr($url, 1) : '';
    }

    private function createMenu()
    {
        $menuService = new MenuService();

        return $menuService->createMenu();
    }


}
