@extends('dashboard.layout')

@section('content')
<div class="w-full gap-auto flex flex-row justify-between items-center">
    <flux:heading size="lg">{{ $title }}</flux:heading>
    <flux:modal.trigger name="create">
        <flux:button variant="primary" size="sm" icon="plus" class="cursor-pointer">Tambah Data</flux:button>
    </flux:modal.trigger>
</div>

@livewire('table', ['model' => $model, 'relationships' => $relationships, 'columns' => $columns])
@livewire('add-row-modal', ['model' => $model])
@endsection