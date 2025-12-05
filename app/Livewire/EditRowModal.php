<?php

namespace App\Livewire;

use Livewire\Component;

class EditRowModal extends Component
{
    public $model;
    public $recordId;
    public $fillable = [];
    public $formData = [];
    public $relationships = [];
    public $manyToManyRelationships = [];
    public $record;

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
            'email' => 'required|email',
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
            'kode' => 'required|string|max:50',
            'deskripsi_tindakan_terapi' => 'required|string|max:500',
            'idkategori' => 'required|exists:kategori,idkategori',
            'idkategori_klinis' => 'required|exists:kategori_klinis,idkategori_klinis',
        ],
        'App\Models\User' => [
            'nama' => 'required|string|max:255',
            'email' => 'required|email',
        ],
        'App\Models\Role' => [
            'nama_role' => 'required|string|max:255',
        ],
        'App\Models\RekamMedis' => [
            'anamnesa' => 'required|string|max:1000',
            'temuan_klinis' => 'required|string|max:1000',
            'diagnosa' => 'required|string|max:1000',
            'idpet' => 'required|exists:pet,idpet',
            'dokter_pemeriksa' => 'required|integer',
            'idreservasi_dokter' => 'nullable|integer',
        ],
        'App\Models\TemuDokter' => [
            'status' => 'required|string|max:1',
            'idpet' => 'required|exists:pet,idpet',
        ],
        'App\Models\Perawat' => [
            'alamat' => 'required|string|max:100',
            'no_hp' => 'required|string|max:45',
            'jenis_kelamin' => 'required|string|max:1',
            'pendidikan' => 'required|string|max:100',
            'id_user' => 'required|exists:user,iduser',
        ],
        'App\Models\Dokter' => [
            'alamat' => 'required|string|max:100',
            'no_hp' => 'required|string|max:45',
            'jenis_kelamin' => 'required|string|max:1',
            'bidang_dokter' => 'required|string|max:100',
            'id_user' => 'required|exists:user,iduser',
        ],
    ];

    protected $listeners = ['editRecord'];

    public function mount($model)
    {
        $this->model = $model;
        $instance = new $model;
        $this->fillable = $instance->getFillable();
        $this->relationships = $this->getRelationships($instance);
        $this->manyToManyRelationships = $this->getManyToManyRelationships($instance);
    }

    public function editRecord($id)
    {
        $this->recordId = $id;
        $this->record = $this->model::find($id);

        if ($this->record) {
            // Populate form data with existing record values
            foreach ($this->fillable as $field) {
                $this->formData[$field] = $this->record->$field ?? '';
            }

            // Populate many-to-many relationships
            foreach ($this->manyToManyRelationships as $relationship) {
                $relation = $this->record->$relationship();
                $relatedModel = $relation->getRelated();
                $relatedTable = $relatedModel->getTable();
                $relatedKey = $relatedModel->getKeyName();
                
                // Qualify the column name with table name to avoid ambiguity
                $this->formData[$relationship] = $relation->pluck($relatedTable . '.' . $relatedKey)->toArray();
            }
            
            // Load detail_rekam_medis for RekamMedis model
            if (class_basename($this->model) === 'RekamMedis') {
                $details = \App\Models\DetailRekamMedis::where('idrekam_medis', $id)->get();
                $this->formData['details'] = $details->map(function($detail) {
                    return [
                        'id' => $detail->iddetail_rekam_medis,
                        'idkode_tindakan_terapi' => $detail->idkode_tindakan_terapi,
                        'detail' => $detail->detail,
                    ];
                })->toArray();
            }
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

    public function update()
    {
        $this->validateFormData();

        $record = $this->model::find($this->recordId);

        if ($record) {
            $record->timestamps = false;

            $fillableData = [];
            $manyToManyData = [];

            foreach ($this->formData as $key => $value) {
                if (in_array($key, $this->fillable)) {
                    $fillableData[$key] = $value;
                } elseif (in_array($key, $this->manyToManyRelationships)) {
                    $manyToManyData[$key] = $value;
                }
            }

            $record->fill($fillableData);
            
            // Handle TemuDokter specific fields
            if (class_basename($this->model) === 'TemuDokter') {
                $record->idrole_user = null;
            }
            
            $record->save();

            // Handle many-to-many relationships
            foreach ($manyToManyData as $relationship => $ids) {
                $record->{$relationship}()->sync($ids);
            }
            
            // Handle detail_rekam_medis for RekamMedis model
            if (class_basename($this->model) === 'RekamMedis' && isset($this->formData['details'])) {
                // Delete existing details
                \App\Models\DetailRekamMedis::where('idrekam_medis', $this->recordId)->delete();
                
                // Create new details
                foreach ($this->formData['details'] as $detail) {
                    if (!empty($detail['idkode_tindakan_terapi'])) {
                        \App\Models\DetailRekamMedis::create([
                            'idrekam_medis' => $this->recordId,
                            'idkode_tindakan_terapi' => $detail['idkode_tindakan_terapi'],
                            'detail' => $detail['detail'] ?? '',
                        ]);
                    }
                }
            }

            $this->dispatch('recordUpdated');
            $this->reset(['formData', 'recordId', 'record']);
            return redirect()->route('dashboard', ['model' => class_basename($this->model)]);
        }
    }
    
    public function addDetail()
    {
        $this->formData['details'][] = [
            'idkode_tindakan_terapi' => '',
            'detail' => '',
        ];
    }
    
    public function removeDetail($index)
    {
        unset($this->formData['details'][$index]);
        $this->formData['details'] = array_values($this->formData['details']); // Re-index array
    }

    private function validateFormData()
    {
        $rules = [];
        $modelClass = $this->model;

        if (isset($this->validationRules[$modelClass])) {
            foreach ($this->validationRules[$modelClass] as $field => $rule) {
                // Handle unique validation for edits - exclude current record
                if (is_string($rule) && str_contains($rule, 'unique:')) {
                    $rule = $rule . ',' . $this->recordId;
                } elseif (is_string($rule) && str_contains($rule, 'email')) {
                    // For email, add unique rule with exception for current record
                    $instance = new $modelClass;
                    $table = $instance->getTable();
                    $primaryKey = $instance->getKeyName();
                    $rule = $rule . '|unique:' . $table . ',' . $field . ',' . $this->recordId . ',' . $primaryKey;
                }
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
            'idpet' => \App\Models\Pet::class,
            'idreservasi_dokter' => \App\Models\TemuDokter::class,
            'id_user' => \App\Models\User::class,
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
        $nameFields = ['nama', 'nama_ras', 'nama_jenis_hewan', 'nama_kategori', 'nama_kategori_klinis', 'nama_role', 'email', 'kode'];

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
        return view('livewire.edit-row-modal');
    }
}
