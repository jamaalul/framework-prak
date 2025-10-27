<?php

namespace App\Livewire;

use Livewire\Component;

class Table extends Component
{
    public $model;
    public $data;
    public $columns = [];
    public $relationships = [];

    public function mount($model, $columns = null, $relationships = [])
    {
        $this->model = $model;
        $this->relationships = $relationships;

        $query = $model::query();
        if (!empty($relationships)) {
            $query->with($relationships);
        }
        $this->data = $query->get();

        // Add computed columns for relationships
        $this->data = $this->data->map(function ($item) use ($relationships) {
            foreach ($relationships as $relationship) {
                if ($relationship === 'role') {
                    $item->roles = $item->role->pluck('nama_role')->join("\n");
                } elseif ($relationship === 'rasHewan') {
                    if ($item->rasHewan instanceof \Illuminate\Database\Eloquent\Collection) {
                        $item->ras_hewan = $item->rasHewan->pluck('nama_ras')->join("\n");
                    } else {
                        $item->ras_hewan = $item->rasHewan->nama_ras ?? '';
                    }
                } elseif ($relationship === 'jenisHewan') {
                    $item->jenis_hewan = $item->jenisHewan->nama_jenis_hewan ?? '';
                } elseif ($relationship === 'kategori') {
                    $item->kategori = $item->kategori->nama_kategori ?? '';
                } elseif ($relationship === 'kategoriKlinis') {
                    $item->kategori_klinis = $item->kategoriKlinis->nama_kategori_klinis ?? '';
                } elseif ($relationship === 'pemilik.user') {
                    $item->pemilik = $item->pemilik->user->nama ?? '';
                }
            }
            return $item;
        });

        if ($columns === null) {
            $this->columns = $this->data->first() ? $this->data->first()->getFillable() : [];
        } else {
            $this->columns = $columns;
        }
    }

    public function render()
    {
        return view('livewire.table');
    }
}
