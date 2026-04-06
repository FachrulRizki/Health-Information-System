@extends('layouts.app')

@section('title', 'Rawat Jalan')

@section('breadcrumb')
    <span class="font-medium" style="color: #1A0A0A;">Rawat Jalan</span>
@endsection

@section('content')
<div class="fade-in">

<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-xl font-bold" style="color: #1A0A0A;">Rawat Jalan</h2>
        <p class="text-sm mt-0.5" style="color: #6B4C4C;">{{ now()->format('d F Y') }}</p>
    </div>
    {{-- Legenda status --}}
    <div class="flex items-center gap-2 text-xs flex-wrap">
        @foreach([
            ['bg' => '#FFFFFF', 'border' => '#D1D5DB', 'color' => '#6B7280', 'label' => 'Menunggu'],
            ['bg' => '#FFFBEB', 'border' => '#F59E0B', 'color' => '#B45309', 'label' => 'Diperiksa'],
            ['bg' => '#F0FFF4', 'border' => '#10B981', 'color' => '#065F46', 'label' => 'Selesai Dokter'],
            ['bg' => '#FEF2F2', 'border' => '#EF4444', 'color' => '#991B1B', 'label' => 'Batal'],
            ['bg' => '#1F2937', 'border' => '#1F2937', 'color' => '#F9FAFB', 'label' => 'Selesai'],
        ] as $leg)
        <span class="px-2.5 py-1 rounded-full font-medium border"
              style="background: {{ $leg['bg'] }}; color: {{ $leg['color'] }}; border-color: {{ $leg['border'] }};">
            {{ $leg['label'] }}
        </span>
        @endforeach
    </div>
</div>

@if(session('success'))
<div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="background: #F0FFF4; border-color: #9AE6B4;">
    <i class="fa-solid fa-circle-check flex-shrink-0" style="color: #276749;"></i>
    <span class="text-sm font-medium" style="color: #276749;">{{ session('success') }}</span>
</div>
@endif

{{-- Date Filter Bar --}}
<div class="bg-white rounded-xl p-4 mb-5" style="border:1px solid #E8D5D5; box-shadow:0 2px 8px rgba(123,29,29,0.06);">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}"
                   class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color:#E8D5D5; color:#1A0A0A; --tw-ring-color:rgba(123,29,29,0.15);">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ $dateTo }}"
                   class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color:#E8D5D5; color:#1A0A0A; --tw-ring-color:rgba(123,29,29,0.15);">
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:#7B1D1D;">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        <a href="{{ route('rme.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border" style="color:#6B4C4C; border-color:#E8D5D5;">
            <i class="fa-solid fa-rotate-left"></i> Hari Ini
        </a>
    </form>
</div>

<div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background: #F9F5F5; border-color: #E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
            <i class="fa-solid fa-stethoscope" style="color: #7B1D1D;"></i>
            Semua Pasien Hari Ini
        </span>
        <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #FFF5F5; color: #7B1D1D;">
            {{ $visits->count() }} pasien
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F9F5F5;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. Rawat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Nama Pasien</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Poli</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Dokter</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Penjamin</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visits as $visit)
                @php
                    [$rowBg, $rowBorder, $statusBg, $statusColor] = match($visit->status) {
                        'dipanggil','dalam_pemeriksaan' => ['#FFFBEB', '#F59E0B', '#FFFBEB', '#B45309'],
                        'farmasi','kasir'               => ['#F0FFF4', '#10B981', '#F0FFF4', '#065F46'],
                        'batal'                         => ['#FEF2F2', '#EF4444', '#FEE2E2', '#991B1B'],
                        'selesai'                       => ['#F3F4F6', '#6B7280', '#1F2937', '#F9FAFB'],
                        default                         => ['#FFFFFF',  '#E5E7EB', '#F9F5F5', '#6B4C4C'],
                    };
                    $penjaminStyle = match($visit->jenis_penjamin) {
                        'bpjs'     => 'background:#EBF8FF;color:#2B6CB0',
                        'asuransi' => 'background:#FAF5FF;color:#6B21A8',
                        default    => 'background:#F9F5F5;color:#6B4C4C',
                    };
                @endphp
                <tr class="transition-colors"
                    style="background: {{ $rowBg }}; border-bottom: 1px solid {{ $rowBorder }}20;">
                    <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;">{{ $visit->no_rawat }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                                {{ strtoupper(substr($visit->patient?->nama_lengkap ?? 'P', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium" style="color: #1A0A0A;">{{ $visit->patient?->nama_lengkap ?? '-' }}</p>
                                <p class="text-xs font-mono" style="color: #6B4C4C;">{{ $visit->patient?->no_rm ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">{{ $visit->poli?->nama_poli ?? '-' }}</td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">{{ $visit->doctor?->nama_dokter ?? '-' }}</td>
                    <td class="px-5 py-3.5">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $penjaminStyle }}">
                            {{ strtoupper($visit->jenis_penjamin) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold border"
                              style="background: {{ $statusBg }}; color: {{ $statusColor }}; border-color: {{ $rowBorder }}40;">
                            {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <a href="{{ route('rme.show', $visit->id) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                           style="background: #7B1D1D;">
                            <i class="fa-solid fa-file-medical"></i>
                            Buka RME
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center">
                        <i class="fa-solid fa-clipboard-list text-3xl mb-3 block" style="color: #E8D5D5;"></i>
                        <p class="text-sm font-medium" style="color: #6B4C4C;">Tidak ada pasien hari ini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection
