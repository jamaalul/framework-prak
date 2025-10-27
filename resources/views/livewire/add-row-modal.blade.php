<flux:modal name="create" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Tambah Data</flux:heading>
            <flux:subheading>Tambah data baru ke dalam tabel.</flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            @foreach($fillable as $field)
                @if(in_array($field, ['idpemilik', 'idras_hewan', 'idjenis_hewan', 'idkategori', 'idkategori_klinis', 'iduser', 'idrole']))
                    <flux:field>
                        <flux:label>{{ ucfirst(str_replace('_', ' ', $field)) }}</flux:label>
                        <flux:select wire:model="formData.{{ $field }}" placeholder="Pilih {{ ucfirst(str_replace('_', ' ', $field)) }}">
                            @php
                                $relatedModel = $this->getRelatedModel($field);
                                $options = $relatedModel ? $relatedModel::all() : [];
                            @endphp
                            @foreach($options as $option)
                                <option value="{{ $option->getKey() }}">{{ $option->getKeyName() ? $option->{$option->getKeyName()} : $option->id }} - {{ $this->getDisplayName($option) }}</option>
                            @endforeach
                        </flux:select>
                        @error('formData.' . $field) <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                @else
                    <flux:field>
                        <flux:label>{{ ucfirst(str_replace('_', ' ', $field)) }}</flux:label>
                        <flux:input wire:model="formData.{{ $field }}" placeholder="Masukkan {{ ucfirst(str_replace('_', ' ', $field)) }}" />
                        @error('formData.' . $field) <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                @endif
            @endforeach

            @foreach($manyToManyRelationships as $relationship)
                <flux:field>
                    <flux:label>{{ ucfirst(str_replace('_', ' ', $relationship)) }}</flux:label>
                    <div class="space-y-2">
                        @php
                            $relatedModel = $this->getRelatedModelForManyToMany($relationship);
                            $options = $relatedModel ? $relatedModel::all() : [];
                        @endphp
                        @foreach($options as $option)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="formData.{{ $relationship }}" value="{{ $option->getKey() }}" class="mr-2">
                                {{ $option->getKeyName() ? $option->{$option->getKeyName()} : $option->id }} - {{ $this->getDisplayName($option) }}
                            </label>
                        @endforeach
                    </div>
                    @error('formData.' . $relationship) <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            @endforeach

            <div class="flex">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">Simpan</flux:button>
            </div>
        </form>
    </div>
</flux:modal>