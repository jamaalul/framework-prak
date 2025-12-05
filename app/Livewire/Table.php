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
        
        // Check if this model has hasMany relationships that should be aggregated
        $hasHasManyRelationships = $this->hasHasManyRelationships($instance, $relationships);
        
        if ($hasHasManyRelationships) {
            // Use Eloquent to properly handle hasMany relationships
            $this->data = $model::with($relationships)->get()->map(function ($item) use ($relationships) {
                // Convert to stdClass to match DB query result format
                $obj = new \stdClass();
                
                // Add all attributes
                foreach ($item->getAttributes() as $key => $value) {
                    $obj->$key = $value;
                }
                
                // Handle relationships
                foreach ($relationships as $relationship) {
                    if ($item->relationLoaded($relationship)) {
                        $related = $item->$relationship;
                        
                        // Convert camelCase relationship name to snake_case for property
                        $propertyName = $this->camelToSnake($relationship);
                        
                        // Check if it's a collection (hasMany/belongsToMany)
                        if ($related instanceof \Illuminate\Database\Eloquent\Collection) {
                            // Aggregate into newline-separated string
                            $obj->$propertyName = $related->pluck($this->getDisplayFieldForRelationship($relationship))->join("\n");
                        } else {
                            // Single relationship (belongsTo/hasOne)
                            $obj->$propertyName = $related ? $this->getDisplayValueFromRelated($related, $relationship) : '';
                        }
                    }
                }
                
                return $obj;
            });
        } else {
            // Use DB query for simpler relationships (original behavior)
            $query = DB::table($instance->getTable());
            $selects = [$instance->getTable() . '.*'];

            foreach ($relationships as $relationship) {
                if ($relationship === 'role') {
                    // This is a many-to-many relationship, handled separately
                } elseif ($relationship === 'rasHewan') {
                    // Skip - will be handled by Eloquent if hasMany
                    if ($instance->getTable() !== 'jenis_hewan') {
                        $query->leftJoin('ras_hewan', $instance->getTable() . '.idras_hewan', '=', 'ras_hewan.idras_hewan');
                        $selects[] = 'ras_hewan.nama_ras as ras_hewan';
                    }
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
                } elseif ($relationship === 'user') {
                    // Handle different id_user field names for different models
                    $userForeignKey = in_array(class_basename($instance), ['Perawat', 'Dokter']) ? 'id_user' : 'iduser';
                    $query->leftJoin('user', $instance->getTable() . '.' . $userForeignKey, '=', 'user.iduser');
                    $selects[] = 'user.nama as user';
                    $selects[] = 'user.email as email';
                } elseif ($relationship === 'pet') {
                    $query->leftJoin('pet', $instance->getTable() . '.idpet', '=', 'pet.idpet');
                    $selects[] = 'pet.nama as pet';
                }
            }

            $this->data = $query->select($selects)->get();
        }

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

    public $deleteId = null;

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function delete()
    {
        if ($this->deleteId) {
            $record = $this->model::find($this->deleteId);
            
            if ($record) {
                $record->delete();
                $this->deleteId = null;
                
                // Refresh the data
                $this->mount($this->model, $this->columns, $this->relationships);
                
                session()->flash('message', 'Record deleted successfully.');
            }
        }
    }

    public function cancelDelete()
    {
        $this->deleteId = null;
    }

    private function hasHasManyRelationships($instance, $relationships)
    {
        foreach ($relationships as $relationship) {
            try {
                $relation = $instance->$relationship();
                if ($relation instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Skip if not a relationship method
            }
        }
        return false;
    }

    private function getDisplayFieldForRelationship($relationship)
    {
        $fieldMap = [
            'rasHewan' => 'nama_ras',
            'jenisHewan' => 'nama_jenis_hewan',
            'kategori' => 'nama_kategori',
            'kategoriKlinis' => 'nama_kategori_klinis',
            'role' => 'nama_role',
        ];

        return $fieldMap[$relationship] ?? 'id';
    }

    private function getDisplayValueFromRelated($related, $relationship)
    {
        $displayField = $this->getDisplayFieldForRelationship($relationship);
        return $related->$displayField ?? $related->id ?? '';
    }

    private function camelToSnake($string)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}
