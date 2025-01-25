<?php

namespace App\Http\Livewire\Recipe;

use App\Models\IngredientUnit;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditUnitModal extends Component
{
    public $title;
    public $mergeUnits = [];
    public $unit;
    public $unitId;

    protected $rules = [
        'title'      => 'required|string|max:255',
        'mergeUnits' => 'array',
    ];

    protected $listeners = [
        'editUnit',
    ];

    public function render()
    {
        return view('livewire.recipe.edit-unit-modal');
    }

    public function editUnit(int $unitId): void
    {
        $this->reset(['mergeUnits']);

        $this->unitId = $unitId;

        $unit = Unit::find($unitId);
        $this->title = $unit->title;
    }

    public function submit(): void
    {
        $this->validate();

        DB::transaction(function () {
            /** @var Unit $mainUnit */
            $mainUnit = Unit::find($this->unitId);
            $mainUnit->update([
                'title' => $this->title,
            ]);

            foreach ($this->mergeUnits as $mergeUnitId) {
                IngredientUnit::where('unit_id', (int) $mergeUnitId)
                    ->update(['unit_id' => $mainUnit->id]);
            }

            Unit::whereIn('id', $this->mergeUnits)->delete();
        });

        $this->mergeUnits = [];
        $this->reset();
        $this->emit('success', 'Units merged successfully.');
    }

    public function hydrate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
