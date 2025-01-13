<?php

namespace App\Http\Livewire\Source;

use App\Models\Source;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

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
                $this->emit('success', __('User updated'));
            } else {
                $this->emit('success', __('New user created'));
            }
        });

        $this->reset();
    }

    public function deleteUser($id)
    {
        // Prevent deletion of current user
        if ($id == Auth::id()) {
            $this->emit('error', 'User cannot be deleted');
            return;
        }

        // Delete the user record with the specified ID
        User::destroy($id);

        // Emit a success event with a message
        $this->emit('success', 'User successfully deleted');
    }

    public function updateUser($id)
    {
        $this->edit_mode = true;

        $user = User::find($id);

        $this->source_id = $user->id;
        $this->saved_avatar = $user->profile_photo_url;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->roles?->first()->name ?? '';
    }

    public function hydrate()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
