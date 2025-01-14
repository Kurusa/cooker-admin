<?php

namespace App\Http\Livewire\Recipe;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ViewIngredientDetails extends Component
{
    public $ingredientId;
    public $ingredientUnits = [];

    protected $listeners = [
        'show_ingredient_details' => 'loadDetails',
    ];

    public function loadDetails(int $ingredientId): void
    {
        $this->ingredientId = $ingredientId;

        $this->ingredientUnits = DB::table('ingredient_units')
            ->join('units', 'ingredient_units.unit_id', '=', 'units.id')
            ->leftJoin('recipe_ingredients', 'ingredient_units.id', '=', 'recipe_ingredients.ingredient_unit_id')
            ->select(
                'ingredient_units.id',
                'units.title as unit_title',
                DB::raw('COUNT(recipe_ingredients.id) as recipe_count')
            )
            ->where('ingredient_units.ingredient_id', $ingredientId)
            ->groupBy('ingredient_units.id', 'units.title')
            ->orderBy('units.title')
            ->get()
            ->toArray();
    }

    public function updateUnitTitle($unitId, $newTitle): void
    {
        DB::table('units')
            ->where('id', $unitId)
            ->update(['title' => $newTitle]);

        $this->loadDetails($this->ingredientId);
        $this->emit('success', __('Unit updated successfully!'));
    }

    public function render()
    {
        return view('livewire.recipe.view-ingredient-details');
    }
}
