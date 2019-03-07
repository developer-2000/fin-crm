<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use App\Services\Menu\MenuService;
use Illuminate\Http\Request;

class MenuController extends BaseController
{
    public function index()
    {
        return view('menu.index', [
            'menuItems' => Menu::with('subMenuRecursive')->main()->menu()->get()
        ]);
    }

    public function edit(Menu $menu)
    {
        return view('menu.edit', [
            'itemMenu' => $menu
        ]);
    }

    public function update(MenuRequest $request, Menu $menu)
    {
        $result = [
            'success' => false,
            'message' => trans('alerts.data-not-changed')
        ];

        try {
            $menu->title = $request->title;
            $menu->type = $request->type;
            $menu->route = $request->route ? $request->route : null;
            $menu->icon = $request->icon ? $request->icon : null;
            $menu->save();

            $result['success'] = true;
            $result['message'] = trans('alerts.data-changed');
        } catch (\Exception $exception) {
            $result['message'] = $exception->getMessage();
        }

        return response()->json($result);
    }

    public function store(MenuRequest $request)
    {
        $result = [
            'success' => false,
            'message' => trans('alerts.data-not-changed')
        ];

        try {
            $position = Menu::main()->menu()->max('position');

            $menu = new Menu();
            $menu->type = $request->type;
            $menu->title = $request->title;
            $menu->route = $request->route ? $request->route : null;
            $menu->icon = $request->icon ? $request->icon : null;
            $menu->position = $position + 1;
            $menu->save();

            $result['html'] = view('menu.item', [
                'menuItems' => collect([$menu])
                ])->render();

            $result['success'] = true;
            $result['message'] = trans('alerts.created');
        } catch (\Exception $exception) {
            $result['message'] = $exception->getMessage();
        }

        return response()->json($result);
    }

    public function destroy(Menu $menu)
    {
        $result = [
            'success' => false,
            'message' => trans('alerts.data-not-changed')
        ];

        try {
            if (MenuService::deleteMenu($menu)) {
                $result['success'] = true;
                $result['message'] = trans('alerts.data-deleted');
            }
        } catch (\Exception $exception) {
            $result['message'] = $exception->getMessage();
        }

        return response()->json($result);
    }

    public function changePosition(Request $request)
    {
        $data = json_decode($request->json, true);

        return response()->json([
            'success' => MenuService::changePosition($data),
            'message' => trans('alerts.data-not-changed')
        ]);
    }
}
