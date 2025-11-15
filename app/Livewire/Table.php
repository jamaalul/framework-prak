<?php

namespace App\Livewire;

use Livewire\Component;
use \Illuminate\Support\Facades\DB;

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

        $instance = new $model;
        $query = DB::table($instance->getTable());

        $selects = [$instance->getTable() . '.*'];

        foreach ($relationships as $relationship) {
            if ($relationship === 'role') {
                // This is a many-to-many relationship, handled separately
            } elseif ($relationship === 'rasHewan') {
                if ($instance->getTable() === 'jenis_hewan') {
                    $query->leftJoin('ras_hewan', 'ras_hewan.idjenis_hewan', '=', 'jenis_hewan.idjenis_hewan');
                } else {
                    $query->leftJoin('ras_hewan', $instance->getTable() . '.idras_hewan', '=', 'ras_hewan.idras_hewan');
                }
                $selects[] = 'ras_hewan.nama_ras as ras_hewan';
            } elseif ($relationship === 'jenisHewan') {
                $query->leftJoin('jenis_hewan', $instance->getTable() . '.idjenis_hewan', '=', 'jenis_hewan.idjenis_hewan');
                $selects[] = 'jenis_hewan.nama_jenis_hewan as jenis_hewan';
            } elseif ($relationship === 'kategori') {
                $query->leftJoin('kategori', $instance->getTable() . '.idkategori', '=', 'kategori.idkategori');
                $selects[] = 'kategori.nama_kategori as kategori';
            } elseif ($relationship === 'kategoriKlinis') {
                $query->leftJoin('kategori_klinis', $instance->getTable() . '.idkategori_klinis', '=', 'kategori_klinis.idkategori_klinis');
                $selects[] = 'kategori_klinis.nama_kategori_klinis as kategori_klinis';
            } elseif ($relationship === 'pemilik.user') {
                $query->leftJoin('pemilik', $instance->getTable() . '.idpemilik', '=', 'pemilik.idpemilik')
                    ->leftJoin('user', 'pemilik.iduser', '=', 'user.iduser');
                $selects[] = 'user.nama as pemilik';
            }
        }

        $this->data = $query->select($selects)->get();

        // Handle many-to-many relationship for roles
        if (in_array('role', $relationships)) {
            $this->data->map(function ($item) use ($instance) {
                $item->roles = \Illuminate\Support\Facades\DB::table('role_user')
                    ->join('role', 'role_user.idrole', '=', 'role.idrole')
                    ->where('role_user.iduser', $item->{$instance->getKeyName()})
                    ->pluck('nama_role')
                    ->join("\n");
                return $item;
            });
        }


        if ($columns === null) {
            $this->columns = $this->data->first() ? array_keys((array) $this->data->first()) : [];
        } else {
            $this->columns = $columns;
        }
    }

    public function render()
    {
        return view('livewire.table');
    }
}
