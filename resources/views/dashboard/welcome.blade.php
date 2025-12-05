@extends('dashboard.layout')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-col items-center justify-center h-full gap-6">
    <flux:heading size="xl" class="text-center">
        Selamat Datang, {{ $user->nama }}!
    </flux:heading>
</div>
@endsection
