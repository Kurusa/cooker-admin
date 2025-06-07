<?php

namespace App\Http\Controllers;

use App\Models\Recipe\RecipeCategory;
use Illuminate\Routing\Controller as BaseController;

class RecipeCategoryController extends BaseController
{
    public function treePage()
    {
        $categories = RecipeCategory::with('children', 'parents')
            ->limit(2)
            ->get();

        $roots = $categories->filter(fn($c) => $c->parents->isEmpty());

        $buildTree = function (RecipeCategory $category, array $visited = []) use (&$buildTree) {
            if (in_array($category->id, $visited)) {
                return null;
            }

            $visited[] = $category->id;

            return [
                'id' => $category->id,
                'title' => $category->title,
                'children' => $category->children
                    ->map(fn($child) => $buildTree($child, $visited))
                    ->filter()
                    ->values()
                    ->all(),
            ];
        };

        $tree = $roots->map(fn($root) => $buildTree($root))->filter()->values()->all();

        return view('recipe-categories.tree', compact('tree'));
    }
}
