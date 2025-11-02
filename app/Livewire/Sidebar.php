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
        $user = User::with('role')->find(Auth::id());

        $modelRoleMapping = [
            'JenisHewan' => ['Administrator'],
            'RasHewan' => ['Administrator'],
            'Kategori' => ['Administrator'],
            'KategoriKlinis' => ['Administrator'],
            'KodeTindakanTerapi' => ['Administrator'],
            'Pet' => ['Administrator', 'Resepsionis'],
            'Role' => ['Administrator'],
            'User' => ['Administrator'],
        ];

        $menuVisibility = [];
        foreach ($modelRoleMapping as $model => $roles) {
            $menuVisibility[$model] = $user->hasAnyRole($roles);
        }

        return view('livewire.sidebar', [
            'user' => $user,
            'menuVisibility' => $menuVisibility,
        ]);
    }
}
