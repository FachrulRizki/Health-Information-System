@extends('layouts.app')

@section('title', 'Rawat Inap')

@section('breadcrumb')
    <span class="font-medium" style="color: #1A0A0A;">Rawat Inap</span>
@endsection

@section('content')
<div class="fade-in">

{{-- Flash message --}}
@if(session('success'))
<div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="background: #F0FFF4; border-color: #9AE6B4;">
    <i class="fa-solid fa-circle-check flex-shrink-0" style="color: #276749;"></i>
    <span class="text-sm font-medium" style="color: #276749;">{{ session('success') }}</span>
</div>
@endif

{{-- Filter Bar --}}
<form method="GET" action="{{ route('inpatient.index') }}"
      class="bg-white rounded-2xl p-4 mb-4 flex flex-wrap items-end gap-3"
      style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="flex-1 min-w-[160px]">
        <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Dari Tanggal</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="w-full border rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2"
               style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);">
    </div>
    <div class="flex-1 min-w-[160px]">
        <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Sampai Tanggal</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="w-full border rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2"
               style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);">
    </div>
    <div class="flex-1 min-w-[160px]">
        <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Cari Pasien</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / No. RM..."
               class="w-full border rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2"
               style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15);">
    </div>
    <div class="flex gap-2">
        <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                style="background: linear-gradient(135deg, #7B1D1D, #5C1414);">
            <i class="fa-solid fa-magnifying-glass"></i> Filter
        </button>
        @if(request()->hasAny(['date_from','date_to','search']))
        <a href="{{ route('inpatient.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border transition-all hover:bg-red-50"
           style="color: #7B1D1D; border-color: #E8D5D5;">
            <i class="fa-solid fa-xmark"></i> Reset
        </a>
        @endif
    </div>
</form>

{{-- Header --}}
<div class="flex items-center justify-between mb-4 flex-wrap gap-3">
    <div>
        <h2 class="text-lg font-bold" style="color: #1A0A0A;">Daftar Pasien Rawat Inap</h2>
        <p class="text-xs mt-0.5" style="color: #6B4C4C;">Pasien yang sedang menjalani rawat inap</p>
    </div>
    <a href="{{ route('inpatient.beds') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90 hover:scale-[1.02]"
       style="background: linear-gradient(135deg, #7B1D1D, #5C1414); box-shadow: 0 2px 8px rgba(123,29,29,0.3);">
        <i class="fa-solid fa-bed-pulse"></i>
        Peta Bed
    </a>
</div>

{{-- Table Card --}}
<div class="bg-white rounded-2xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 flex items-center justify-between" style="background: #F9F5F5; border-bottom: 1px solid #E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
            <i class="fa-solid fa-bed-pulse" style="color: #7B1D1D;"></i>
            Pasien Aktif
        </span>
        <span class="text-xs px-2.5 py-1 rounded-full font-semibold"
              style="background: #FFF5F5; color: #7B1D1D; border: 1px solid #E8D5D5;">
            {{ $records->count() }} pasien
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F9F5F5;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Pasien</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. Rawat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Dokter PJ</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Poli</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Kamar / Bed</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Penjamin</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #E8D5D5;">
                @forelse($records as $record)
                    @php $visit = $record->visit; @endphp
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                     style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                                    {{ strtoupper(substr($visit?->patient?->nama_lengkap ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium" style="color: #1A0A0A;">{{ $visit?->patient?->nama_lengkap ?? '-' }}</p>
                                    <p class="text-xs font-mono" style="color: #6B4C4C;">{{ $visit?->patient?->no_rm ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;">{{ $visit?->no_rawat ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">{{ $visit?->doctor?->nama_dokter ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">{{ $visit?->poli?->nama_poli ?? '-' }}</td>
                        <td class="px-5 py-3.5">
                            @if($record->bed)
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-bed text-xs" style="color: #7B1D1D;"></i>
                                    <span class="text-sm" style="color: #1A0A0A;">{{ $record->bed?->room?->nama_kamar ?? '—' }}</span>
                                    <span class="text-xs font-mono px-1.5 py-0.5 rounded"
                                          style="background: #FFF5F5; color: #7B1D1D; border: 1px solid #E8D5D5;">
                                        {{ $record->bed->kode_bed }}
                                    </span>
                                </div>
                            @else
                                <span style="color: #6B4C4C;">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $penjaminStyle = match($visit?->jenis_penjamin) {
                                    'bpjs'     => 'background:#EBF8FF;color:#2B6CB0;border:1px solid #BEE3F8;',
                                    'asuransi' => 'background:#FAF5FF;color:#6B21A8;border:1px solid #E9D8FD;',
                                    default    => 'background:#F9F5F5;color:#6B4C4C;border:1px solid #E8D5D5;',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $penjaminStyle }}">
                                {{ strtoupper($visit?->jenis_penjamin ?? '-') }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $statusStyle = match($record->status_pulang ?? 'aktif') {
                                    'aktif'   => 'background:#FFF5F5;color:#7B1D1D;border:1px solid #E8D5D5;',
                                    'pulang'  => 'background:#F0FFF4;color:#276749;border:1px solid #9AE6B4;',
                                    'dirujuk' => 'background:#FFFBEB;color:#B7791F;border:1px solid #F6E05E;',
                                    default   => 'background:#F9F5F5;color:#6B4C4C;border:1px solid #E8D5D5;',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $statusStyle }}">
                                {{ ucfirst(str_replace('_', ' ', $record->status_pulang ?? 'Aktif')) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <a href="{{ route('inpatient.show', $visit?->id) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90 hover:scale-[1.03]"
                               style="background: linear-gradient(135deg, #7B1D1D, #5C1414);">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-14 text-center">
                            <i class="fa-solid fa-bed text-3xl mb-3 block" style="color: #E8D5D5;"></i>
                            <p class="text-sm font-medium" style="color: #6B4C4C;">Tidak ada pasien rawat inap aktif</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection
