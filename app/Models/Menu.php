<?php

namespace App\Models;

use App\Scopes\MenuScope;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    const MAIN = 'main';

    /**
     * relations
     *
     * One to Many relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function subMenu()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function subMenuRecursive()
    {
        return $this->subMenu()->with('subMenuRecursive');
    }

    /**
     * scopes
     *
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new MenuScope);
    }

    public function scopeMain($query)
    {
        return $query->where('type', self::MAIN);
    }

    public function scopeMenu($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSubMenu($query, $parentId)
    {
        return $query->whereNotNull('parent_id')
            ->whereHas($parentId, function ($q) use ($parentId) {
                $q->where('parent_id', $parentId);
            });
    }

    public static function getType()
    {
        return [
            self::MAIN,
        ];
    }
}
