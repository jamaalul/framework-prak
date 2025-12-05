<div class="mt-6">
    <flux:modal name="delete-confirm">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Confirm Deletion</flux:heading>
                <flux:subheading>Are you sure you want to delete this record? This action cannot be undone.</flux:subheading>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost" wire:click="cancelDelete">Cancel</flux:button>
                </flux:modal.close>
                
                <flux:modal.close>
                    <flux:button variant="danger" wire:click="delete">Delete</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    @foreach($columns as $column)
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                            {{ ucfirst(str_replace('_', ' ', $column)) }}
                        </th>
                    @endforeach
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($data as $row)
                    <tr>
                        @foreach($columns as $column)
                            <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100 whitespace-pre-line">
                                @if(str_contains($column, '.'))
                                    @php
                                        $parts = explode('.', $column);
                                        $value = $row;
                                        foreach($parts as $part) {
                                            if (is_numeric($part)) {
                                                $value = $value[$part] ?? '';
                                            } else {
                                                $value = $value->$part ?? '';
                                            }
                                        }
                                        echo $value;
                                    @endphp
                                @elseif($column === 'status' && class_basename($model) === 'TemuDokter')
                                    @if($row->status == '1')
                                        <br>Selesai
                                    @elseif($row->status == '0')
                                        <br>Mendatang
                                    @else
                                        {{ $row->$column }}
                                    @endif
                                @elseif($column === 'jenis_kelamin' && class_basename($model) === 'Perawat')
                                    @if($row->jenis_kelamin == 'L')
                                        Laki-laki
                                    @elseif($row->jenis_kelamin == 'P')
                                        Perempuan
                                    @else
                                        {{ $row->$column }}
                                    @endif
                                @elseif($column === 'jenis_kelamin' && class_basename($model) === 'Dokter')
                                    @if($row->jenis_kelamin == 'L')
                                        Laki-laki
                                    @elseif($row->jenis_kelamin == 'P')
                                        Perempuan
                                    @else
                                        {{ $row->$column }}
                                    @endif
                                @else
                                    {{ $row->$column }}
                                @endif
                            </td>
                        @endforeach
                        <td class="px-6 py-4 text-sm">
                            @php
                                $instance = new $model;
                                $primaryKey = $instance->getKeyName();
                                $recordId = $row->$primaryKey;
                            @endphp
                            <div class="flex gap-2">
                                @if(class_basename($model) === 'RekamMedis')
                                    <a href="{{ route('rekam_medis.show', $recordId) }}">
                                        <flux:button variant="primary" size="sm" icon="eye">
                                            View
                                        </flux:button>
                                    </a>
                                @endif
                                
                                <flux:modal.trigger name="edit">
                                    <flux:button 
                                        wire:click="$dispatch('editRecord', { id: {{ $recordId }} })"
                                        variant="primary"
                                        size="sm"
                                    >
                                        Edit
                                    </flux:button>
                                </flux:modal.trigger>
                                
                                <flux:modal.trigger name="delete-confirm">
                                    <flux:button 
                                        wire:click="confirmDelete({{ $recordId }})"
                                        variant="danger"
                                        size="sm"
                                    >
                                        Delete
                                    </flux:button>
                                </flux:modal.trigger>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
