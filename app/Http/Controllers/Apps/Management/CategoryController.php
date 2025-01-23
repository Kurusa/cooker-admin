<?php

namespace App\Http\Controllers\Apps\Management;

use App\DataTables\Management\CategoriesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Recipe;

class CategoryController extends Controller
{
    public function index(CategoriesDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.management.categories.list');
    }

    public function show(Category $category)
    {
        return response()->json([
            'id' => $category->id,
            'title' => $category->title,
            'parent_id' => $category->parent_id,
            'child_categories' => $category->children()->pluck('id')->toArray(),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update([
            'title' => $request->get('title'),
        ]);

        if ($request->get('parentId')) {
            $category->update([
                'parent_id' => $request->get('parentId'),
            ]);
        }

        if (!empty($request->get('childCategories'))) {
            Category::whereIn('id', $request->get('childCategories'))->update(['parent_id' => $category->id]);
        }

        if (!empty($request->get('mergeCategories'))) {
            foreach ($request->get('mergeCategories') as $categoryId) {
                Recipe::where('category_id', $categoryId)
                    ->update(['category_id' => $category->id]);
            }

            Category::whereIn('id', $request->get('mergeCategories'))->delete();
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['success' => true]);
    }

    public function getDetails(Category $category)
    {
        $html = view('pages.apps.management.categories.partials.view-category-details', compact('category'))->render();

        return response()->json(['html' => $html]);
    }
}
