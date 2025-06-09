<?php

namespace App\Http\Controllers;

use App\Models\Recipe\RecipeCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CategoryTreeController extends Controller
{
    public function index(): View
    {
        return view('categories-tree');
    }

    public function children(?int $parentId = null): JsonResponse
    {
        $query = RecipeCategory::query();

        if ($parentId) {
            $query->whereHas('parents', fn($q) => $q->where('recipe_category_parent_map.parent_id', $parentId));
        } else {
            $query->whereDoesntHave('parents');
        }

        $categories = $query
            ->withCount('children')
            ->orderBy('title')
            ->get()
            ->map(fn(RecipeCategory $c) => [
                'id' => $c->id,
                'title' => $c->title,
                'children_count' => $c->children_count,
                'has_children' => $c->children_count > 0,
            ]);

        return response()->json($categories);
    }
}
