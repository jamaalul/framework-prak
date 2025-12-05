<flux:modal name="edit" class="w-full overflow-x-hidden">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Edit Data</flux:heading>
            <flux:subheading>Edit data yang sudah ada.</flux:subheading>
        </div>

        <form wire:submit="update" class="space-y-6">
            @foreach ($fillable as $field)
                @if (in_array($field, [
                        'idpemilik',
                        'idras_hewan',
                        'idjenis_hewan',
                        'idkategori',
                        'idkategori_klinis',
                        'iduser',
                        'idrole',
                        'idpet',
                        'idreservasi_dokter',
                        'id_user',
                    ]))
                    <flux:field>
                        <flux:label>{{ ucfirst(str_replace('_', ' ', $field)) }}</flux:label>
                        <flux:select wire:model="formData.{{ $field }}"
                            placeholder="Pilih {{ ucfirst(str_replace('_', ' ', $field)) }}">
                            @php
                                $relatedModel = $this->getRelatedModel($field);
                                // Special handling for idreservasi_dokter - only show completed appointments
                                if ($field === 'idreservasi_dokter') {
                                    $options = \App\Models\TemuDokter::where('status', '1')->with('pet')->get();
                                } else {
                                    $options = $relatedModel ? $relatedModel::all() : [];
                                }
                            @endphp
                            @foreach ($options as $option)
                                <option value="{{ $option->getKey() }}">
                                    @if($field === 'idreservasi_dokter')
                                        {{ $option->no_urut }} - {{ $option->pet->nama ?? 'N/A' }} ({{ $option->waktu_daftar }})
                                    @else
                                        {{ $option->getKeyName() ? $option->{$option->getKeyName()} : $option->id }} -
                                        {{ $this->getDisplayName($option) }}
                                    @endif
                                </option>
                            @endforeach
                        </flux:select>
                        @error('formData.' . $field)
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                @else
                    <flux:field>
                        <flux:label>{{ ucfirst(str_replace('_', ' ', $field)) }}</flux:label>
                        @if($field === 'status' && class_basename($model) === 'TemuDokter')
                            <flux:select wire:model="formData.{{ $field }}" placeholder="Pilih Status">
                                <option value="0">Mendatang</option>
                                <option value="1">Selesai</option>
                            </flux:select>
                        @elseif($field === 'jenis_kelamin' && class_basename($model) === 'Perawat')
                            <flux:select wire:model="formData.{{ $field }}" placeholder="Pilih Jenis Kelamin">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </flux:select>
                        @elseif($field === 'jenis_kelamin' && class_basename($model) === 'Dokter')
                            <flux:select wire:model="formData.{{ $field }}" placeholder="Pilih Jenis Kelamin">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </flux:select>
                        @else
                            <flux:input wire:model="formData.{{ $field }}"
                                placeholder="Masukkan {{ ucfirst(str_replace('_', ' ', $field)) }}" />
                        @endif
                        @error('formData.' . $field)
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                @endif
            @endforeach

            @foreach ($manyToManyRelationships as $relationship)
                <flux:field>
                    <flux:label>{{ ucfirst(str_replace('_', ' ', $relationship)) }}</flux:label>
                    <div class="space-y-2">
                        @php
                                $relatedModel = $this->getRelatedModelForManyToMany($relationship);
                                $options = $relatedModel ? $relatedModel::all() : [];
                            @endphp
                        @foreach ($options as $option)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="formData.{{ $relationship }}"
                                    value="{{ $option->getKey() }}" class="mr-2">
                                {{ $option->getKeyName() ? $option->{$option->getKeyName()} : $option->id }} -
                                {{ $this->getDisplayName($option) }}
                            </label>
                        @endforeach
                    </div>
                    @error('formData.' . $relationship)
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            @endforeach

            {{-- Detail Rekam Medis Section --}}
            @if(class_basename($model) === 'RekamMedis')
                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4">
                    <flux:heading size="md" class="mb-3 mt-3">Detail Tindakan & Terapi</flux:heading>
                    
                    <div class="space-y-2 mb-3 mt-3">
                        @if(isset($formData['details']) && count($formData['details']) > 0)
                            @foreach($formData['details'] as $index => $detail)
                                <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-lg border border-zinc-200 dark:border-zinc-700">
                                    <div class="flex justify-between items-start mb-2">
                                        <flux:subheading>Detail {{ $index + 1 }}</flux:subheading>
                                        <flux:button wire:click="removeDetail({{ $index }})" variant="danger" size="sm">Hapus</flux:button>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <flux:field>
                                            <flux:label>Kode Tindakan Terapi</flux:label>
                                            <flux:select wire:model="formData.details.{{ $index }}.idkode_tindakan_terapi" placeholder="Pilih Kode Tindakan">
                                                @foreach(\App\Models\KodeTindakanTerapi::all() as $kode)
                                                    <option value="{{ $kode->idkode_tindakan_terapi }}">{{ $kode->kode }} - {{ $kode->deskripsi_tindakan_terapi }}</option>
                                                @endforeach
                                            </flux:select>
                                        </flux:field>
                                        
                                        <flux:field>
                                            <flux:label>Detail</flux:label>
                                            <flux:textarea wire:model="formData.details.{{ $index }}.detail" placeholder="Masukkan detail tindakan" rows="2"></flux:textarea>
                                        </flux:field>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-zinc-500 dark:text-zinc-400 text-sm">Belum ada detail tindakan</p>
                        @endif
                    </div>
                    
                    <flux:button wire:click="addDetail" type="button" variant="ghost" icon="plus" size="sm" class="mt-3">Tambah Detail</flux:button>
                </div>
            @endif

            <div class="flex">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">Update</flux:button>
            </div>
        </form>
    </div>
</flux:modal>
