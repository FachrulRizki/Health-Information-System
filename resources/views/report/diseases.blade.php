@extends('layouts.app')

@section('title', 'Laporan Penyakit ICD-10')

@section('breadcrumb')
    <span style="color: #64748B;">Laporan</span>
    <i class="fa-solid fa-chevron-right text-xs mx-2" style="color: #cbd5e1;"></i>
    <span class="font-medium" style="color: #0F172A;">Penyakit ICD-10</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold" style="color: #0F172A;">Laporan Penyakit Berdasarkan ICD-10</h2>
        <p class="text-sm mt-0.5" style="color: #64748B;">Rekap diagnosa berdasarkan kode ICD-10</p>
    </div>
</div>

{{-- Filter Form --}}
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-5" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #FEF2F2;">
            <i class="fa-solid fa-virus text-xs" style="color: #EF4444;"></i>
        </div>
        <h3 class="text-sm font-semibold" style="color: #0F172A;">Filter Laporan</h3>
    </div>
    <form method="GET" action="{{ route('report.diseases') }}">
        <div class="grid grid-cols-2 gap-4 mb-4">
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
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white"
                    style="background: #2563EB;">
                <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
            </button>
            <a href="{{ route('report.diseases') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border border-slate-200 hover:bg-slate-50 transition-colors"
               style="color: #64748B;">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        </div>
    </form>
</div>

@if($diseases->isNotEmpty())
    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm mb-4 w-fit" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <i class="fa-solid fa-virus" style="color: #EF4444;"></i>
        <span style="color: #64748B;">Total kode ICD-10:</span>
        <span class="font-bold" style="color: #0F172A;">{{ $diseases->count() }}</span>
    </div>
@endif

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F8FAFC;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide w-12" style="color: #64748B;">No.</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Kode ICD-10</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Deskripsi Penyakit</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Jumlah Kasus</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($diseases as $i => $row)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-sm" style="color: #94a3b8;">{{ $i + 1 }}</td>
                        <td class="px-5 py-3.5">
                            <span class="font-mono font-bold px-2.5 py-1 rounded-lg text-sm" style="background: #EFF6FF; color: #1D4ED8;">
                                {{ $row->icd10_code }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-sm" style="color: #0F172A;">{{ $row->icd10Code?->deskripsi ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-right">
                            <span class="font-bold text-sm px-3 py-1 rounded-full" style="background: #FEF2F2; color: #B91C1C;">
                                {{ $row->total_kasus }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center">
                            <i class="fa-solid fa-virus text-3xl mb-3 block" style="color: #cbd5e1;"></i>
                            <p class="text-sm font-medium" style="color: #64748B;">
                                {{ request()->hasAny(['date_from','date_to'])
                                    ? 'Tidak ada data diagnosa.'
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
