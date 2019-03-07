<?php

namespace App\Services\Menu;

use App\Models\Menu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Menu\Html;
use Spatie\Menu\Link;

class MenuService
{
    public static function deleteMenu(Menu $menu)
    {
        foreach ($menu->subMenu as $subMenu) {
            self::deleteMenu($subMenu);
        }

        $menu->subMenu()->delete();

        return $menu->delete();
    }

    public static function changePosition($data, $parentId = null)
    {
        $i = 1;
        if ($data) {
            foreach ($data as $category) {
                Menu::where('id', $category['id'])
                    ->update([
                        'position' => $i++,
                        'parent_id' => $parentId,
                    ]);
                if (isset($category['children'])) {
                    self::changePosition($category['children'], $category['id']);
                }
            }
        }

        return true;
    }

    /**
     * @param Collection $menuItems
     * @param \Spatie\Menu\Menu $menu
     * @param $menuAll collect()->keyBy('id')->groupBy('parent_id', function ($item) {return $item;}, true);
     * @param null $parentIcon
     * @param bool $subMenu
     */
    public function createNodeMenu(Collection $menuItems, \Spatie\Menu\Menu $menu, $menuAll, $parentIcon = null, $subMenu = false)
    {
        if ($menuItems->isNotEmpty()) {
            foreach ($menuItems as $item) {
                //search children
                if (!empty($menuAll[$item->id])) {
                    $children = $menuAll[$item->id];
                    if ($children->count() > 1) {
                        $subMenu = \Spatie\Menu\Menu::new();
                        $subMenu->addClass('submenu');
                        $this->createNodeMenu($children, $subMenu, $menuAll, $parentIcon ? null : $item->icon, true);

                        $title = self::getTitleMenu(self::transTitle($item->title), $parentIcon ? null : $item->icon, true);
                        $link = Link::to('#', $title)
                            ->addClass('dropdown-toggle');

                        $menu->submenu($link, $subMenu->setActiveClass(''));
                    } else {
                        $this->createNodeMenu($children, $menu, $menuAll, $parentIcon ?? $item->icon, $subMenu);
                    }
                } else {
                    $icon = $item->icon;

                    if ($parentIcon && $subMenu) {//if multiple submenu
                        $icon = null;
                    } else if ($parentIcon && !$subMenu) {//if count submenu = 1
                        $icon = $parentIcon;
                    }
                    $menu->add($this->createElement($item, $icon, $item->active));
                }
            }
        }

        $menu->setExactActiveClass('');
    }

    public function createMenu()
    {
        $menuItems = $this->getUserMenu();

        $menu = \Spatie\Menu\Menu::new();
        $this->createNodeMenu($menuItems->first() ?? collect(), $menu, $menuItems);
        $menu->addClass('nav')
            ->addClass('nav-pills')
            ->addClass('nav-stacked');

        return $menu;
    }

    private function getUserMenu()
    {
        $allMenu= Menu::main()->get();
        $menuItems = [];

        if ($allMenu->isNotEmpty()) {
            foreach ($allMenu as &$menu) {
                $route = Route::getRoutes()->getByName($menu->route);

                if ($route) {
                    if (!$this->checkMenuPermission($route)) {
                        continue;
                    }
                }

                $menu->active = self::checkCurrentRoute($menu->route);
                $menuItems[] = $menu;
            }
        }

        $menuItems = collect($menuItems)->keyBy('id')->groupBy('parent_id', function ($item) {
            return $item;
        }, true);

        return $menuItems;
    }


    private function checkRoute($route)
    {
        if (Route::has($route)) {
            return \route($route);
        }

        return $route ?? '#';
    }

    private function createElement(Menu $menu, $icon = null, $active = false)
    {
        $title = self::transTitle($menu->title);
        $link = Link::to($this->checkRoute($menu->route), self::getTitleMenu($title, $icon));
        if ($active) {
            $link->setParentAttribute('class', '');
            $link->setAttribute('class', 'active');
            $link->setActive();
        }

        if (!$menu->route && !$menu->icon) {
            $link = Html::raw($title)
                ->addParentClass('nav-header')
                ->addParentClass('nav-header-first')
                ->addParentClass('hidden-sm')
                ->addParentClass('hidden-xs');
        }

        return $link;
    }

    public static function checkCurrentRoute($routeName)
    {
        return $routeName && $routeName == Route::currentRouteName();
    }

    private static function transTitle($title)
    {
        $trans = trans($title);
        return is_array($trans) ? $title : $trans;
    }

    public static function getTitleMenu($title, $icon = null, $dropDown = false)
    {
        $html = $icon ? '<i class="fa ' . $icon . '"></i>': '';
        $html .= '<span>' . $title . '</span>';
        $html .= $dropDown ? '<i class="fa fa-angle-right drop-icon"></i>' : '';

        return $html;
    }

    public function checkMenuPermission($route)
    {
        $action = $route->getAction();
        $permissions = [];
        $canText = 'can:';

        if (!empty($action['middleware'])) {
            foreach ($action['middleware'] as $value) {
                if (is_int(strpos($value, $canText))) {
                    $permissions[] = str_replace($canText, '', $value);
                }
            }

            if ($permissions) {
                foreach ($permissions as $permission) {
                    if (Auth::user()->can($permission)) {
                        return true;
                    }
                }

                return false;
            }
        }

        return true;
    }
}