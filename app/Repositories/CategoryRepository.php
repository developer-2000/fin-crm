<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public static function scriptsCategories()
    {
        return Category::where('entity', 'script')->get();
    }
    public static function postsCategories()
    {
        return Category::where('entity', 'post')->get();
    }
}