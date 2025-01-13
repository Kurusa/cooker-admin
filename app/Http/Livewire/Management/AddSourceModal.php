<?php

namespace App\Http\Livewire\Management;

use App\Models\Source;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddSourceModal extends Component
{
    public $source_id;
    public $url;

    public $edit_mode = true;

    protected $rules = [
        'url' => 'required|string',
    ];

    protected $listeners = [
        'delete_source' => 'deleteSource',
        'update_source' => 'updateSource',
    ];

    public function render()
    {
        return view('livewire.source.add-source-modal');
    }

    public function submit(): void
    {
        $this->validate();

        DB::transaction(function () {
            $data = [
                'url' => $this->url,
            ];

            /** @var Source $source */
            $source = $this->source_id ? Source::find($this->source_id) : Source::create($data);

            if ($this->edit_mode) {
                $source->update($data);

                $this->emit('success', __('Source updated'));
            } else {
                $this->emit('success', __('New source created'));
            }
        });

        $this->reset();
    }

    public function deleteSource(int $id): void
    {
        Source::destroy($id);

        $this->emit('success', 'Source successfully deleted');
    }

    public function hydrate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
