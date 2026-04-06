@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('breadcrumb')
    <span style="color: #64748B;">Laporan</span>
    <i class="fa-solid fa-chevron-right text-xs mx-2" style="color: #cbd5e1;"></i>
    <span class="font-medium" style="color: #0F172A;">Keuangan</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold" style="color: #0F172A;">Laporan Keuangan</h2>
        <p class="text-sm mt-0.5" style="color: #64748B;">Rekap pendapatan dan transaksi keuangan</p>
    </div>
</div>

{{-- Filter Form --}}
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-5" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #ECFDF5;">
            <i class="fa-solid fa-coins text-xs" style="color: #10B981;"></i>
        </div>
        <h3 class="text-sm font-semibold" style="color: #0F172A;">Filter Laporan</h3>
    </div>
    <form method="GET" action="{{ route('report.financial') }}">
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
            <a href="{{ route('report.financial') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border border-slate-200 hover:bg-slate-50 transition-colors"
               style="color: #64748B;">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        </div>
    </form>
</div>

@if($report)
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #ECFDF5;">
                <i class="fa-solid fa-money-bill-wave text-lg" style="color: #10B981;"></i>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide" style="color: #64748B;">Pendapatan Tunai</p>
                <p class="text-lg font-bold mt-0.5" style="color: #10B981;">Rp {{ number_format($report['total_tunai'], 0, ',', '.') }}</p>
                <p class="text-xs" style="color: #94a3b8;">Umum + Asuransi</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: #EFF6FF;">
                <i class="fa-solid fa-shield-halved text-lg" style="color: #2563EB;"></i>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide" style="color: #64748B;">Klaim BPJS</p>
                <p class="text-lg font-bold mt-0.5" style="color: #2563EB;">Rp {{ number_format($report['total_bpjs'], 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="rounded-xl border p-5 flex items-center gap-4" style="background: linear-gradient(135deg, #1E3A5F, #2563EB); border-color: #2563EB; box-shadow: 0 4px 12px rgba(37,99,235,0.2);">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-white/20">
                <i class="fa-solid fa-sack-dollar text-lg text-white"></i>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-blue-200">Total Pendapatan</p>
                <p class="text-lg font-bold mt-0.5 text-white">Rp {{ number_format($report['grand_total'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Breakdown by payment method --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-5" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-2" style="background: #F8FAFC;">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #EFF6FF;">
                <i class="fa-solid fa-chart-pie text-xs" style="color: #2563EB;"></i>
            </div>
            <h3 class="text-sm font-semibold" style="color: #0F172A;">Rincian per Metode Pembayaran</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background: #F8FAFC;">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Metode Pembayaran</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Jumlah Transaksi</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($report['by_method'] as $method => $data)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5">
                                @php
                                    $penjaminStyle = match($method) {
                                        'bpjs'     => 'background:#EFF6FF;color:#1D4ED8',
                                        'asuransi' => 'background:#F5F3FF;color:#7C3AED',
                                        default    => 'background:#F8FAFC;color:#475569',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $penjaminStyle }}">
                                    {{ strtoupper($method) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold" style="color: #0F172A;">{{ $data['count'] }}</td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold" style="color: #0F172A;">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-6 text-center text-sm" style="color: #94a3b8;">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detail transactions --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between" style="background: #F8FAFC;">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #FFFBEB;">
                    <i class="fa-solid fa-receipt text-xs" style="color: #F59E0B;"></i>
                </div>
                <h3 class="text-sm font-semibold" style="color: #0F172A;">Detail Transaksi</h3>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #FFFBEB; color: #92400E;">
                {{ $report['bills']->count() }} transaksi
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background: #F8FAFC;">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">No. Rawat</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Nama Pasien</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Metode</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($report['bills'] as $bill)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs" style="color: #64748B;">{{ $bill->visit?->no_rawat ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-sm" style="color: #0F172A;">{{ $bill->visit?->tanggal_kunjungan?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-3.5 font-medium" style="color: #0F172A;">{{ $bill->visit?->patient?->nama_lengkap ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $penjaminStyle = match($bill->payment_method) {
                                        'bpjs'     => 'background:#EFF6FF;color:#1D4ED8',
                                        'asuransi' => 'background:#F5F3FF;color:#7C3AED',
                                        default    => 'background:#F8FAFC;color:#475569',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $penjaminStyle }}">
                                    {{ strtoupper($bill->payment_method) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-semibold" style="color: #0F172A;">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-6 text-center text-sm" style="color: #94a3b8;">Tidak ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background: #ECFDF5;">
            <i class="fa-solid fa-coins text-2xl" style="color: #10B981;"></i>
        </div>
        <p class="font-semibold mb-1" style="color: #0F172A;">Laporan Keuangan</p>
        <p class="text-sm" style="color: #64748B;">Gunakan filter di atas untuk menampilkan laporan keuangan.</p>
    </div>
@endif
@endsection
