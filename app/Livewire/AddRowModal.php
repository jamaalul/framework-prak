<?php

namespace App\Livewire;

use Livewire\Component;

class AddRowModal extends Component
{
    public $model;
    public $fillable = [];
    public $formData = [];
    public $relationships = [];
    public $manyToManyRelationships = [];

    public function mount($model)
    {
        $this->model = $model;
        $instance = new $model;
        $this->fillable = $instance->getFillable();
        $this->relationships = $this->getRelationships($instance);
        $this->manyToManyRelationships = $this->getManyToManyRelationships($instance);
        foreach ($this->fillable as $field) {
            $this->formData[$field] = '';
        }
        foreach ($this->manyToManyRelationships as $relationship) {
            $this->formData[$relationship] = [];
        }
    }

    private function getRelationships($instance)
    {
        $relationships = [];
        $reflection = new \ReflectionClass($instance);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($method->class === get_class($instance) && !$method->isStatic()) {
                $returnType = $method->getReturnType();
                if ($returnType && $returnType->getName() === 'Illuminate\Database\Eloquent\Relations\Relation') {
                    $relationships[] = $method->getName();
                }
            }
        }

        return $relationships;
    }

    private function getManyToManyRelationships($instance)
    {
        $manyToManyRelationships = [];
        $reflection = new \ReflectionClass($instance);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($method->class === get_class($instance) && !$method->isStatic()) {
                try {
                    $relation = $instance->{$method->getName()}();
                    if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                        $manyToManyRelationships[] = $method->getName();
                    }
                } catch (\Exception $e) {
                    // Skip if method is not a relationship
                }
            }
        }

        return $manyToManyRelationships;
    }

    public function save()
    {
        $this->validate([
            'formData.*' => 'required',
        ]);

        $newRecord = new $this->model;
        $newRecord->timestamps = false; // Disable timestamps

        // Separate fillable data from many-to-many relationships
        $fillableData = [];
        $manyToManyData = [];

        foreach ($this->formData as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $fillableData[$key] = $value;
            } elseif (in_array($key, $this->manyToManyRelationships)) {
                $manyToManyData[$key] = $value;
            }
        }

        $newRecord->fill($fillableData);
        $newRecord->save();

        // Handle many-to-many relationships
        foreach ($manyToManyData as $relationship => $ids) {
            if (!empty($ids)) {
                $newRecord->{$relationship}()->attach($ids);
            }
        }

        $this->dispatch('rowAdded');
        $this->reset('formData');
        return redirect()->route('dashboard', ['model' => class_basename($this->model)]);
    }

    public function getRelatedModel($field)
    {
        $mappings = [
            'idpemilik' => \App\Models\Pemilik::class,
            'idras_hewan' => \App\Models\RasHewan::class,
            'idjenis_hewan' => \App\Models\JenisHewan::class,
            'idkategori' => \App\Models\Kategori::class,
            'idkategori_klinis' => \App\Models\KategoriKlinis::class,
            'iduser' => \App\Models\User::class,
            'idrole' => \App\Models\Role::class,
        ];

        return $mappings[$field] ?? null;
    }

    public function getRelatedModelForManyToMany($relationship)
    {
        $mappings = [
            'role' => \App\Models\Role::class,
        ];

        return $mappings[$relationship] ?? null;
    }

    public function getDisplayName($option)
    {
        $nameFields = ['nama', 'nama_ras', 'nama_jenis_hewan', 'nama_kategori', 'nama_kategori_klinis', 'nama_role', 'email'];

        foreach ($nameFields as $field) {
            if (isset($option->$field)) {
                return $option->$field;
            }
        }

        // Check for related user model if it exists
        if (isset($option->user) && $option->user) {
            return $option->user->nama;
        }

        return $option->getKey();
    }

    public function render()
    {
        return view('livewire.add-row-modal');
    }
}