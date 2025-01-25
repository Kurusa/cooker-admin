<?php

namespace App\Http\Livewire\Recipe;

use App\Models\Recipe;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddRecipeModal extends Component
{
    public $recipe_id;
    public $title;
    public $complexity;
    public $advice;
    public $time;
    public $portions;
    public $source_url;
    public $category_id;
    public $image_url;

    public $edit_mode = false;

    protected $rules = [
        'title'       => 'required|string|max:255',
        'complexity'  => 'required|in:easy,medium,hard',
        'advice'      => 'nullable|string|max:1024',
        'time'        => 'required|integer|min:1',
        'portions'    => 'required|integer|min:1',
        'source_url'  => 'nullable|url|max:255',
        'category_id' => 'required|integer|exists:categories,id',
        'image_url'   => 'nullable|url|max:255',
    ];

    protected $listeners = [
        'delete_recipe' => 'deleteRecipe',
        'update_recipe' => 'updateRecipe',
    ];

    public function render()
    {
        return view('livewire.recipe.add-recipe-modal');
    }

    public function submit(): void
    {
        $this->validate();

        DB::transaction(function () {
            $data = [
                'title'       => $this->title,
                'complexity'  => $this->complexity,
                'advice'      => $this->advice,
                'time'        => $this->time,
                'portions'    => $this->portions,
                'source_url'  => $this->source_url,
                'category_id' => $this->category_id,
                'image_url'   => $this->image_url,
            ];

            /** @var Recipe $recipe */
            $recipe = $this->recipe_id ? Recipe::find($this->recipe_id) : new Recipe();

            if ($this->edit_mode) {
                $recipe->update($data);
                $this->emit('success', __('Recipe updated'));
            } else {
                $recipe->fill($data);
                $recipe->save();
                $this->emit('success', __('New recipe created'));
            }
        });

        $this->reset();
    }

    public function deleteRecipe(int $id): void
    {
        Recipe::destroy($id);
        $this->emit('success', __('Recipe successfully deleted'));
    }

    public function hydrate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
