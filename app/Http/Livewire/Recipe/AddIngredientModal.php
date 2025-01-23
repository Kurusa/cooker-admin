<?php

namespace App\Http\Livewire\Recipe;

use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddIngredientModal extends Component
{
    public $ingredient_id;
    public $title;
    public $unit;

    public $edit_mode = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'unit' => 'nullable|string|max:50',
    ];

    protected $listeners = [
        'delete_ingredient' => 'deleteIngredient',
        'update_ingredient' => 'updateIngredient',
    ];

    public function render()
    {
        return view('livewire.recipe.add-ingredient-modal');
    }

    public function submit(): void
    {
        $this->validate();

        DB::transaction(function () {
            $data = [
                'title' => $this->title,
                'unit' => $this->unit,
            ];

            /** @var Ingredient $ingredient */
            $ingredient = $this->ingredient_id ? Ingredient::find($this->ingredient_id) : new Ingredient();

            if ($this->edit_mode) {
                $ingredient->update($data);
                $this->emit('success', __('Ingredient updated'));
            } else {
                $ingredient->fill($data);
                $ingredient->save();
                $this->emit('success', __('New ingredient created'));
            }
        });

        $this->reset();
    }

    public function deleteIngredient(int $id): void
    {
        Ingredient::destroy($id);
        $this->emit('success', __('Ingredient successfully deleted'));
    }

    public function updateIngredient(int $id): void
    {
        $this->edit_mode = true;

        $ingredient = Ingredient::find($id);

        $this->ingredient_id = $ingredient->id;
        $this->title = $ingredient->title;
        $this->unit = $ingredient->unit;
    }

    public function hydrate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
