@extends('layouts.app')

@section('title', 'Admisi')

@section('breadcrumb')
    <span class="font-medium" style="color: #1A0A0A;">Admisi</span>
@endsection

@section('content')
<div class="fade-in">

{{-- Header --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-xl font-bold flex items-center gap-2" style="color: #1A0A0A;">
            Admisi Rawat Inap
            <span class="text-sm px-2.5 py-1 rounded-full font-semibold" style="background: #FFF5F5; color: #7B1D1D;">
                {{ $visits->count() }} pasien
            </span>
        </h2>
        <p class="text-sm mt-0.5" style="color: #6B4C4C;">Pasien menunggu konfirmasi admisi — {{ now()->format('d F Y') }}</p>
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
        <a href="{{ route('admisi.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border" style="color:#6B4C4C; border-color:#E8D5D5;">
            <i class="fa-solid fa-rotate-left"></i> Hari Ini
        </a>
    </form>
</div>

<div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 border-b" style="background: #F9F5F5; border-color: #E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
            <i class="fa-solid fa-bed-pulse" style="color: #7B1D1D;"></i>
            Daftar Pasien Menunggu Admisi
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
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Waktu Daftar</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #E8D5D5;">
                @forelse($visits as $visit)
                @php
                    $statusStyle = match($visit->status) {
                        'dalam_pemeriksaan' => 'background:#DBEAFE;color:#1D4ED8',
                        'kasir'             => 'background:#DCFCE7;color:#166534',
                        'selesai'           => 'background:#374151;color:#F9FAFB',
                        default             => 'background:#F9F5F5;color:#6B4C4C',
                    };
                @endphp
                <tr class="hover:bg-red-50 transition-colors">
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
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $statusStyle }}">
                            {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-sm" style="color: #6B4C4C;">
                        {{ $visit->created_at?->format('H:i') ?? '-' }}
                    </td>
                    <td class="px-5 py-3.5">
                        <a href="{{ route('admisi.confirm', $visit->id) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                           style="background: #7B1D1D; box-shadow: 0 1px 4px rgba(123,29,29,0.3);">
                            <i class="fa-solid fa-bed-pulse"></i>
                            Konfirmasi Admisi
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center"
                                 style="background: #F9F5F5; border: 1px solid #E8D5D5;">
                                <i class="fa-solid fa-bed text-2xl" style="color: #E8D5D5;"></i>
                            </div>
                            <p class="text-sm font-semibold" style="color: #1A0A0A;">Tidak ada pasien menunggu admisi</p>
                            <p class="text-xs" style="color: #6B4C4C;">Pasien yang memerlukan rawat inap akan muncul di sini</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection
