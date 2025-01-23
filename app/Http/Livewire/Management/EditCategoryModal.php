<?php

namespace App\Http\Livewire\Management;

use App\Models\IngredientCategory;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditCategoryModal extends Component
{
    public $title;
    public $parentId;

    protected $rules = [
        'title' => 'required|string|max:255',
        'parentId' => 'int|exists:categories,id',
    ];

    protected $listeners = [
        'editCategory',
    ];

    public function render()
    {
        return view('livewire.management.edit-category-modal');
    }

    public function editCategory(int $categoryId): void
    {
        $this->reset(['mergeCategories']);

        $this->categoryId = $categoryId;

        $category = Category::find($categoryId);
        $this->title = $category->title;
    }

    public function submit(): void
    {
        $this->validate();

        DB::transaction(function () {
            /** @var Category $category */
            $category = Category::find($this->categoryId);
            $category->update([
                'title' => $this->title,
            ]);

            if ($this->parentId) {
                $category->update([
                    'parent_id' => $this->parentId,
                ]);
            }
        });

        $this->reset();
        $this->emit('success', 'Category updated successfully.');
    }

    public function hydrate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
