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

    protected $validationRules = [
        'App\Models\Pet' => [
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'warna_tanda' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:J,B',
            'idpemilik' => 'required|exists:pemilik,idpemilik',
            'idras_hewan' => 'required|exists:ras_hewan,idras_hewan',
        ],
        'App\Models\Pemilik' => [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pemilik,email',
            'password' => 'required|string|min:8',
            'no_wa' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
            'iduser' => 'required|exists:user,iduser',
        ],
        'App\Models\RasHewan' => [
            'nama_ras' => 'required|string|max:255',
            'idjenis_hewan' => 'required|exists:jenis_hewan,idjenis_hewan',
        ],
        'App\Models\JenisHewan' => [
            'nama_jenis_hewan' => 'required|string|max:255',
        ],
        'App\Models\Kategori' => [
            'nama_kategori' => 'required|string|max:255',
        ],
        'App\Models\KategoriKlinis' => [
            'nama_kategori_klinis' => 'required|string|max:255',
        ],
        'App\Models\KodeTindakanTerapi' => [
            'kode' => 'required|string|max:50|unique:kode_tindakan_terapi,kode',
            'deskripsi_tindakan_terapi' => 'required|string|max:500',
            'idkategori' => 'required|exists:kategori,idkategori',
            'idkategori_klinis' => 'required|exists:kategori_klinis,idkategori_klinis',
        ],
        'App\Models\User' => [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|string|min:8',
        ],
        'App\Models\Role' => [
            'nama_role' => 'required|string|max:255|unique:role,nama_role',
        ],
    ];

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
            if ($method->class === get_class($instance) && !$method->isStatic() && $method->getNumberOfParameters() === 0) {
                try {
                    $relation = $instance->{$method->getName()}();
                    if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany) {
                        $manyToManyRelationships[] = $method->getName();
                    }
                } catch (\Throwable $e) {
                    // Skip if method is not a relationship
                }
            }
        }

        return $manyToManyRelationships;
    }

    public function save()
    {
        $this->validateFormData();

        $newRecord = new $this->model;
        $newRecord->timestamps = false;

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

    private function validateFormData()
    {
        $rules = [];
        $modelClass = $this->model;

        if (isset($this->validationRules[$modelClass])) {
            foreach ($this->validationRules[$modelClass] as $field => $rule) {
                $rules["formData.{$field}"] = $rule;
            }
        } else {
            // Fallback to generic required validation if no specific rules defined
            $rules['formData.*'] = 'required';
        }

        $this->validate($rules);
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