@extends('layouts.app')

@section('title', 'Laporan Kunjungan')

@section('breadcrumb')
    <span style="color: #64748B;">Laporan</span>
    <i class="fa-solid fa-chevron-right text-xs mx-2" style="color: #cbd5e1;"></i>
    <span class="font-medium" style="color: #0F172A;">Kunjungan Pasien</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold" style="color: #0F172A;">Laporan Kunjungan Pasien</h2>
        <p class="text-sm mt-0.5" style="color: #64748B;">Rekap data kunjungan berdasarkan filter</p>
    </div>
</div>

{{-- Filter Form --}}
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-5" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #EFF6FF;">
            <i class="fa-solid fa-filter text-xs" style="color: #2563EB;"></i>
        </div>
        <h3 class="text-sm font-semibold" style="color: #0F172A;">Filter Laporan</h3>
    </div>
    <form method="GET" action="{{ route('report.visits') }}">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-4">
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400"
                       style="color: #0F172A;">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400"
                       style="color: #0F172A;">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Poli</label>
                <select name="poli_id"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400 appearance-none"
                        style="color: #0F172A;">
                    <option value="">Semua Poli</option>
                    @foreach($polis as $poli)
                        <option value="{{ $poli->id }}" {{ ($filters['poli_id'] ?? '') == $poli->id ? 'selected' : '' }}>
                            {{ $poli->nama_poli }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Dokter</label>
                <select name="doctor_id"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400 appearance-none"
                        style="color: #0F172A;">
                    <option value="">Semua Dokter</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ ($filters['doctor_id'] ?? '') == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->nama_dokter }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: #374151;">Jenis Penjamin</label>
                <select name="jenis_penjamin"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:border-blue-400 appearance-none"
                        style="color: #0F172A;">
                    <option value="">Semua</option>
                    <option value="umum"     {{ ($filters['jenis_penjamin'] ?? '') === 'umum'     ? 'selected' : '' }}>Umum</option>
                    <option value="bpjs"     {{ ($filters['jenis_penjamin'] ?? '') === 'bpjs'     ? 'selected' : '' }}>BPJS</option>
                    <option value="asuransi" {{ ($filters['jenis_penjamin'] ?? '') === 'asuransi' ? 'selected' : '' }}>Asuransi</option>
                </select>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
                    style="background: #2563EB;">
                <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
            </button>
            <a href="{{ route('report.visits') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border border-slate-200 hover:bg-slate-50 transition-colors"
               style="color: #64748B;">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        </div>
    </form>
</div>

@if($visits->isNotEmpty())
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
            <i class="fa-solid fa-chart-bar" style="color: #2563EB;"></i>
            <span style="color: #64748B;">Total kunjungan:</span>
            <span class="font-bold" style="color: #0F172A;">{{ $visits->count() }}</span>
        </div>
    </div>
@endif

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F8FAFC;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">No. Rawat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Tanggal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Nama Pasien</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">No. RM</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Poli</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Dokter</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Penjamin</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($visits as $visit)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 font-mono text-xs" style="color: #64748B;">{{ $visit->no_rawat }}</td>
                        <td class="px-5 py-3.5 text-sm" style="color: #0F172A;">{{ $visit->tanggal_kunjungan?->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5 font-medium" style="color: #0F172A;">{{ $visit->patient?->nama_lengkap ?? '-' }}</td>
                        <td class="px-5 py-3.5 font-mono text-xs" style="color: #64748B;">{{ $visit->patient?->no_rm ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-sm" style="color: #0F172A;">{{ $visit->poli?->nama_poli ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-sm" style="color: #0F172A;">{{ $visit->doctor?->nama_dokter ?? '-' }}</td>
                        <td class="px-5 py-3.5">
                            @php
                                $penjaminStyle = match($visit->jenis_penjamin) {
                                    'bpjs'     => 'background:#EFF6FF;color:#1D4ED8',
                                    'asuransi' => 'background:#F5F3FF;color:#7C3AED',
                                    default    => 'background:#F8FAFC;color:#475569',
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $penjaminStyle }}">
                                {{ strtoupper($visit->jenis_penjamin) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="background: #ECFDF5; color: #065F46;">
                                {{ $visit->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <i class="fa-solid fa-chart-bar text-3xl mb-3 block" style="color: #cbd5e1;"></i>
                            <p class="text-sm font-medium" style="color: #64748B;">
                                {{ request()->hasAny(['date_from','date_to','poli_id','doctor_id','jenis_penjamin'])
                                    ? 'Tidak ada data kunjungan.'
                                    : 'Gunakan filter di atas untuk menampilkan laporan.' }}
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
