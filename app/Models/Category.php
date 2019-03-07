<?php

namespace App\Models;

use Illuminate\Support\Collection;
use PhpParser\ErrorHandler\Collecting;

class Category extends Model
{
    protected $fillable = ['entity', 'partner_id', 'category_id', 'name'];

    const ENTITY_POST = 'post';

    const ENTITY_SCRIPT = 'script';

    const ENTITY_PRODUCT = 'product';

    const ENTITY_DOCUMENTATION = 'documentation';

    public static $entities = [
        self::ENTITY_POST    => 'Новости',
        self::ENTITY_SCRIPT  => 'Скрипты',
        self::ENTITY_PRODUCT => 'Товары',
        self::ENTITY_DOCUMENTATION => 'Документация',
    ];

    /**
     * get posts
     * @return \Illuminate\Database\Eloquent\Relations\HasMany;
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * get scripts
     * @return \Illuminate\Database\Eloquent\Relations\HasMany;
     */
    public function scripts()
    {
        return $this->hasMany(ScriptDetail::class);
    }

    /**
     * get scripts
     * @return \Illuminate\Database\Eloquent\Relations\HasMany;
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function documentations()
    {
        return $this->hasMany(Documentation::class, 'category_id');
    }

    public static function getAllCategories($parentId = 0)
    {
        $categories = self::where('parent_id', $parentId)
        ->get();

        if ($categories->isNotEmpty()) {
            foreach ($categories as &$category) {
                $category->subCategories = self::getAllCategories($category->id);
            }
        }

        return $categories;
    }

    public static function updateEntitySubCategory(Collection $categories, $entity)
    {
        if ($categories->count()) {
            foreach ($categories as $category) {
                $category->entity = $entity;
                $category->save();
                self::updateEntitySubCategory($category->subCategories, $entity);
            }
        }
    }

    /**
     * @param $data array
     */
    public static function changePositionCategories($data, $parentId = 0)
    {
        $i = 1;
        if ($data) {
            foreach ($data as $category) {
                self::where('id', $category['id'])
                    ->update([
                        'position' => $i++,
                        'parent_id' => $parentId,
                    ]);
                if (isset($category['children'])) {
                    self::changePositionCategories($category['children'], $category['id']);
                }
            }
        }
    }

    public static function deleteCategory(Category $category)
    {
        if ($category->subCategories->isNotEmpty()) {
            foreach ($category->subCategories as $subCategory) {
                self::deleteCategory($subCategory);
            }
        }

        return $category->delete();
    }

    public static function checkRelation(Category $category)
    {
        $result = true;
        foreach (self::$entities as $entity => $title) {
            $function = str_plural($entity);
            $relation = $category->$function;
            if ($relation->count()) {
                return false;
            }
        }

        if ($category->subCategories->isNotEmpty()) {
            foreach ($category->subCategories as $subCategory) {
               $result = self::checkRelation($subCategory);

               if (!$result) {
                   return $result;
               }
            }
        }

        return $result;
    }
}
