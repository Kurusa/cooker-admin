<?php

namespace App\Http\Livewire\Recipe;

use App\Models\Step;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddStepModal extends Component
{
    public $step_id;
    public $recipe_id;
    public $description;
    public $image_url;
    public $index;

    public $edit_mode = false;

    protected array $rules = [
        'recipe_id' => 'required|integer',
        'description' => 'required|string|max:1024',
        'image_url' => 'nullable|string',
        'index' => 'required|integer',
    ];

    protected $listeners = [
        'delete_step' => 'deleteStep',
        'update_step' => 'updateStep',
    ];

    public function render()
    {
        return view('livewire.recipe.add-step-modal');
    }

    public function submit(): void
    {
        $this->validate();

        DB::transaction(function () {
            $data = [
                'recipe_id' => $this->recipe_id,
                'description' => $this->description,
                'image_url' => $this->image_url,
                'index' => $this->index,
            ];

            /** @var Step $step */
            $step = $this->step_id ? Step::find($this->step_id) : new Step();

            if ($this->edit_mode) {
                $step->update($data);
                $this->emit('success', __('Step updated'));
            } else {
                $step->fill($data);
                $step->save();
                $this->emit('success', __('New step created'));
            }
        });

        $this->reset();
    }

    public function deleteStep(int $id): void
    {
        Step::destroy($id);
        $this->emit('success', __('Step successfully deleted'));
    }

    public function updateStep(int $id): void
    {
        $this->edit_mode = true;

        $step = Step::find($id);

        $this->step_id = $step->id;
        $this->recipe_id = $step->recipe_id;
        $this->description = $step->description;
        $this->image_url = $step->image_url;
        $this->index = $step->index;
    }

    public function hydrate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
