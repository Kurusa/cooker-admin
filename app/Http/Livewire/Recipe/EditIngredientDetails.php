<?php

namespace App\Http\Livewire\Recipe;

use Livewire\Component;

class EditIngredientDetails extends Component
{
    public $ingredientId;
    public $recipeIngredients = [];

    protected $listeners = [
        'loadIngredientDetails' => 'loadDetails',
    ];

    public function loadDetails($ingredientId)
    {
        $this->ingredientId = $ingredientId;

        $this->recipeIngredients = RecipeIngredient::where('ingredient_id', $this->ingredientId)
            ->join('recipes', 'recipes.id', '=', 'recipe_ingredients.recipe_id')
            ->select('recipe_ingredients.*', 'recipes.title as recipe_title')
            ->get()
            ->toArray();
    }

    public function updateQuantity($id, $quantity)
    {
        RecipeIngredient::where('id', $id)->update(['quantity' => $quantity]);

        $this->emit('success', __('Quantity updated successfully!'));
        $this->loadDetails($this->ingredientId); // Reload the updated data
    }

    public function updateUnit($id, $unit)
    {
        RecipeIngredient::where('id', $id)->update(['unit' => $unit]);

        $this->emit('success', __('Unit updated successfully!'));
        $this->loadDetails($this->ingredientId); // Reload the updated data
    }

    public function render()
    {
        return view('livewire.recipe.edit-ingredient-details');
    }
}
