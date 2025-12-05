@extends('dashboard.layout')

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Detail Rekam Medis</flux:heading>
            <flux:subheading>{{ $rekamMedis->pet->nama ?? 'N/A' }}</flux:subheading>
        </div>
        <a href="{{ route('dashboard', ['model' => 'RekamMedis']) }}">
            <flux:button variant="ghost" icon="arrow-left">Kembali</flux:button>
        </a>
    </div>

    {{-- Master Information Card --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
        <flux:heading size="lg" class="mb-4">Informasi Rekam Medis</flux:heading>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <flux:subheading class="mb-1">Tanggal</flux:subheading>
                <p class="text-zinc-900 dark:text-zinc-100">{{ $rekamMedis->created_at ?? '-' }}</p>
            </div>

            <div>
                <flux:subheading class="mb-1">Nama Pet</flux:subheading>
                <p class="text-zinc-900 dark:text-zinc-100">{{ $rekamMedis->pet->nama ?? '-' }}</p>
            </div>

            <div>
                <flux:subheading class="mb-1">Pemilik</flux:subheading>
                <p class="text-zinc-900 dark:text-zinc-100">{{ $rekamMedis->pet->pemilik->user->nama ?? '-' }}</p>
            </div>

            <div>
                <flux:subheading class="mb-1">Ras Hewan</flux:subheading>
                <p class="text-zinc-900 dark:text-zinc-100">{{ $rekamMedis->pet->rasHewan->nama_ras ?? '-' }}</p>
            </div>

            <div class="md:col-span-2">
                <flux:subheading class="mb-1">Anamnesa</flux:subheading>
                <p class="text-zinc-900 dark:text-zinc-100">{{ $rekamMedis->anamnesa ?? '-' }}</p>
            </div>

            <div class="md:col-span-2">
                <flux:subheading class="mb-1">Temuan Klinis</flux:subheading>
                <p class="text-zinc-900 dark:text-zinc-100">{{ $rekamMedis->temuan_klinis ?? '-' }}</p>
            </div>

            <div class="md:col-span-2">
                <flux:subheading class="mb-1">Diagnosa</flux:subheading>
                <p class="text-zinc-900 dark:text-zinc-100">{{ $rekamMedis->diagnosa ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Detail Rekam Medis Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6">
        <flux:heading size="lg" class="mb-4">Detail Tindakan & Terapi</flux:heading>
        
        @if($rekamMedis->detailRekamMedis->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                No
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                Kode Tindakan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                Deskripsi Tindakan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-300 uppercase tracking-wider">
                                Detail
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($rekamMedis->detailRekamMedis as $index => $detail)
                            <tr>
                                <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $detail->kodeTindakanTerapi->kode ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ $detail->kodeTindakanTerapi->deskripsi_tindakan_terapi ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100 whitespace-pre-line">
                                    {{ $detail->detail ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <flux:subheading>Tidak ada detail tindakan & terapi</flux:subheading>
            </div>
        @endif
    </div>
</div>
@endsection
