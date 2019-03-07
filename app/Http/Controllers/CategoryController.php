<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    public function index(Request $request)
    {
        return view('categories.index', [
            'categoriesByEntities' => Category::getAllCategories()->groupBy('entity'), /*todo фильтр ннннннадо?*/
            'entities'   => Category::$entities
        ]);
    }

    public function createAjax(Request $request)
    {
        $entities =  implode(",", array_keys(Category::$entities));
        $this->validate($request, [
            'name'      => 'required|string|max:255|min:1',
            'entity'    => 'required|string|in:' . $entities,
            'parent_id' => 'nullable|int|exists:' . Category::tableName() . ',id'
        ]);

        $parentId = $request->parent_id ? $request->parent_id : 0;
        $currentPosition = Category::where([
            ['parent_id', $parentId],
            ['entity', $request->entity]
        ])->max('position');

        $category = new Category();
        $category->name = $request->name;
        $category->entity = $request->entity;
        $category->parent_id = $parentId;
        $category->position = $currentPosition + 1;
        $result = $category->save();

        return response()->json([
            'success' => $result,
            'id'      => $category->id
        ]);
    }

    public function editAjax(Request $request)
    {
        $entities = '|in:' . Category::ENTITY_POST . ',' . Category::ENTITY_PRODUCT . ',' . Category::ENTITY_SCRIPT;
        $rules = [
            'pk' => 'required|integer|exists:' . Category::tableName() . ',id',
            'value' => 'required|string|min:1',
            'name'  => 'required|in:name,entity',
        ];
        if ($request->name == 'entity') {
            $rules['value'] .= $entities;
        }

        $this->validate($request, $rules);

        $category = Category::with('subCategories')->find($request->pk);
        $category->{$request->name} = $request->value;
        $result = $category->save();

        if ($request->name == 'entity') {
            Category::updateEntitySubCategory($category->subCategories, $request->value);
        }

        return response()->json(['success' => $result]);
    }

    public function changePositionAjax(Request $request)
    {
        $this->validate($request, [
            'json'  => 'required|json',
        ]);

        try {
            $data = json_decode($request->json, true);

            Category::changePositionCategories($data);

            return response()->json([
                'success'   => true,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'success'   => false,
                'message'   => $exception->getMessage(),
            ]);
        }
    }

    public function deleteAjax(Request $request)
    {
        $category = Category::findOrFail($request->id);

        $check = Category::checkRelation($category);
        $result = $check;
        if ($check) {
            $result = Category::deleteCategory($category);
        }

        return response()->json([
            'success'   => $result,
            'check'     => $check,
        ]);
    }
}
