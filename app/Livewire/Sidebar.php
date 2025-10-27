<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Sidebar extends Component
{
    public $model;

    public function mount($model = null)
    {
        $this->model = $model;
    }

    public function render()
    {
        return view('livewire.sidebar', [
            'user' => User::with('role')->find(Auth::id()),
        ]);
    }
}
