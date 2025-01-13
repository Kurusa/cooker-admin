<?php

namespace App\Http\Livewire\Management;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddCategoryModal extends Component
{
    public $category_id;
    public $title;

    public $edit_mode = true;

    protected $rules = [
        'title' => 'required|string',
    ];

    protected $listeners = [
        'delete_category' => 'deleteCategory',
        'update_category' => 'updateCategory',
    ];

    public function render()
    {
        return view('livewire.category.add-category-modal');
    }

    public function submit(): void
    {
        $this->validate();

        DB::transaction(function () {
            $data = [
                'title' => $this->title,
            ];

            /** @var Category $category */
            $category = $this->category_id ? Category::find($this->category_id) : Category::create($data);

            if ($this->edit_mode) {
                $category->update($data);

                $this->emit('success', __('Category updated'));
            } else {
                $this->emit('success', __('New category created'));
            }
        });

        $this->reset();
    }

    public function deleteCategory(int $id): void
    {
        Category::destroy($id);

        $this->emit('success', 'Category successfully deleted');
    }

    public function hydrate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
