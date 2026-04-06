@extends('layouts.app')

@section('title', 'Berkas Digital — ' . $patient->nama_lengkap)

@section('breadcrumb')
    <a href="{{ route('berkas-digital.index') }}" class="hover:opacity-70 transition-opacity" style="color: #6B4C4C;">Berkas Digital</a>
    <span style="color: #E8D5D5;">/</span>
    <span class="font-medium" style="color: #1A0A0A;">{{ $patient->nama_lengkap }}</span>
@endsection

@section('content')
<div class="fade-in" style="min-width:0; overflow-x:hidden;">

{{-- Header Pasien --}}
<div class="bg-white rounded-2xl p-5 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
                 style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                {{ strtoupper(substr($patient->nama_lengkap, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold" style="color: #1A0A0A;">{{ $patient->nama_lengkap }}</h2>
                <div class="flex items-center gap-3 mt-1 flex-wrap text-xs" style="color: #6B4C4C;">
                    <span><i class="fa-solid fa-id-card mr-1"></i>No. RM: <span class="font-mono font-semibold">{{ $patient->no_rm }}</span></span>
                    @if($patient->no_bpjs)
                    <span>|</span>
                    <span><i class="fa-solid fa-shield-halved mr-1"></i>BPJS: <span class="font-mono">{{ $patient->no_bpjs }}</span></span>
                    @endif
                    @if($patient->tanggal_lahir)
                    <span>|</span>
                    <span><i class="fa-solid fa-cake-candles mr-1"></i>{{ $patient->tanggal_lahir->format('d/m/Y') }}</span>
                    @endif
                    <span>|</span>
                    <span><i class="fa-solid fa-venus-mars mr-1"></i>{{ $patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 text-xs flex-wrap">
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl" style="background:#FFF5F5; color:#7B1D1D;">
                <i class="fa-solid fa-person-walking"></i>
                <span class="font-semibold">{{ $rawatJalan->count() }}</span> Rawat Jalan
            </div>
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl" style="background:#EBF8FF; color:#2B6CB0;">
                <i class="fa-solid fa-bed"></i>
                <span class="font-semibold">{{ $rawatInap->count() }}</span> Rawat Inap
            </div>
        </div>
    </div>
</div>

{{-- Filter Tanggal --}}
<div class="bg-white rounded-2xl p-4 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}"
                   class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15); color:#1A0A0A;">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color: #6B4C4C;">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}"
                   class="border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color: #E8D5D5; --tw-ring-color: rgba(123,29,29,0.15); color:#1A0A0A;">
        </div>
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                style="background: linear-gradient(135deg, #7B1D1D, #5C1414);">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        @if($startDate || $endDate)
        <a href="{{ route('claims.index', $patient->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border transition-all hover:bg-red-50"
           style="color: #6B4C4C; border-color: #E8D5D5;">
            <i class="fa-solid fa-rotate-left"></i> Reset
        </a>
        @endif
    </form>
</div>

{{-- Sub-menu Tab --}}
<div class="flex gap-1 mb-5 p-1 rounded-xl w-fit" style="background: #F3E8E8;">
    <a href="{{ route('claims.index', array_merge(['patientId' => $patient->id], request()->except('tab'))) }}?tab=rawat_jalan{{ $startDate ? '&start_date='.$startDate : '' }}{{ $endDate ? '&end_date='.$endDate : '' }}"
       class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold transition-all"
       style="{{ $tab === 'rawat_jalan' ? 'background:#7B1D1D; color:#fff;' : 'color:#7B1D1D;' }}">
        <i class="fa-solid fa-person-walking"></i>
        Rawat Jalan
        <span class="text-xs px-1.5 py-0.5 rounded-full font-bold"
              style="{{ $tab === 'rawat_jalan' ? 'background:rgba(255,255,255,0.2); color:#fff;' : 'background:#FFF0F0; color:#7B1D1D;' }}">
            {{ $rawatJalan->count() }}
        </span>
    </a>
    <a href="{{ route('claims.index', $patient->id) }}?tab=rawat_inap{{ $startDate ? '&start_date='.$startDate : '' }}{{ $endDate ? '&end_date='.$endDate : '' }}"
       class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold transition-all"
       style="{{ $tab === 'rawat_inap' ? 'background:#7B1D1D; color:#fff;' : 'color:#7B1D1D;' }}">
        <i class="fa-solid fa-hospital-user"></i>
        Rawat Inap
        <span class="text-xs px-1.5 py-0.5 rounded-full font-bold"
              style="{{ $tab === 'rawat_inap' ? 'background:rgba(255,255,255,0.2); color:#fff;' : 'background:#FFF0F0; color:#7B1D1D;' }}">
            {{ $rawatInap->count() }}
        </span>
    </a>
</div>

{{-- ═══ TAB: RAWAT JALAN ═══ --}}
@if($tab === 'rawat_jalan')
<div class="bg-white rounded-2xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background: #F9F5F5; border-color: #E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
            <i class="fa-solid fa-person-walking" style="color: #7B1D1D;"></i>
            Berkas Rawat Jalan
        </span>
        <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #FFF5F5; color: #7B1D1D;">
            {{ $rawatJalan->count() }} kunjungan
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F9F5F5;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. Rawat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Tanggal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Poli</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Dokter</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Penjamin</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Dokumen</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #E8D5D5;">
                @forelse($rawatJalan as $visit)
                <tr class="hover:bg-red-50 transition-colors">
                    <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;">{{ $visit->no_rawat }}</td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">{{ $visit->tanggal_kunjungan?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">{{ $visit->poli?->nama_poli ?? '-' }}</td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">{{ $visit->doctor?->nama_dokter ?? '-' }}</td>
                    <td class="px-5 py-3.5">
                        @php
                            $pStyle = match($visit->jenis_penjamin) {
                                'bpjs'     => 'background:#EBF8FF;color:#2B6CB0',
                                'asuransi' => 'background:#FAF5FF;color:#6B21A8',
                                default    => 'background:#F9F5F5;color:#6B4C4C',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="{{ $pStyle }}">
                            {{ strtoupper($visit->jenis_penjamin) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex gap-1 flex-wrap">
                            @if($visit->medicalRecord)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#F0FFF4;color:#276749;">RME</span>
                            @endif
                            @if($visit->diagnoses->count())
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#EBF8FF;color:#2B6CB0;">Diagnosa</span>
                            @endif
                            @if($visit->bill)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#FAF5FF;color:#6B21A8;">Tagihan</span>
                            @endif
                            @if($visit->no_sep)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#FFFBEB;color:#B45309;">SEP</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('claims.show', $visit->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                               style="background: #7B1D1D;">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                            <a href="{{ route('claims.export-pdf', $visit->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all hover:bg-red-50"
                               style="color: #7B1D1D; border-color: #E8D5D5;">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center">
                        <i class="fa-solid fa-person-walking text-3xl mb-3 block" style="color: #E8D5D5;"></i>
                        <p class="text-sm font-medium" style="color: #6B4C4C;">Tidak ada data rawat jalan{{ ($startDate || $endDate) ? ' pada periode ini' : '' }}.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ═══ TAB: RAWAT INAP ═══ --}}
@if($tab === 'rawat_inap')
<div class="bg-white rounded-2xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background: #F9F5F5; border-color: #E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
            <i class="fa-solid fa-hospital-user" style="color: #7B1D1D;"></i>
            Berkas Rawat Inap
        </span>
        <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #FFF5F5; color: #7B1D1D;">
            {{ $rawatInap->count() }} kunjungan
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F9F5F5;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">No. Rawat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Tgl Masuk</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Tgl Keluar</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Kamar/Bed</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Penjamin</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Dokumen</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #E8D5D5;">
                @forelse($rawatInap as $visit)
                @php $inpatient = $visit->inpatientRecord; @endphp
                <tr class="hover:bg-red-50 transition-colors">
                    <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;">{{ $visit->no_rawat }}</td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">{{ $inpatient?->tanggal_masuk?->format('d/m/Y') ?? $visit->tanggal_kunjungan?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">
                        {{ $inpatient?->tanggal_keluar?->format('d/m/Y') ?? '-' }}
                    </td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">
                        @if($inpatient?->bed)
                            <p class="font-medium">{{ $inpatient->bed->kode_bed }}</p>
                            <p class="text-xs" style="color:#6B4C4C;">{{ $inpatient->bed->room?->nama_kamar ?? '-' }}</p>
                        @else
                            <span style="color:#9B7B7B;">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        @php
                            $pStyle = match($visit->jenis_penjamin) {
                                'bpjs'     => 'background:#EBF8FF;color:#2B6CB0',
                                'asuransi' => 'background:#FAF5FF;color:#6B21A8',
                                default    => 'background:#F9F5F5;color:#6B4C4C',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="{{ $pStyle }}">
                            {{ strtoupper($visit->jenis_penjamin) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        @php
                            $statusPulang = $inpatient?->status_pulang ?? 'dirawat';
                            $spStyle = match($statusPulang) {
                                'dirawat'                => 'background:#EBF8FF;color:#2B6CB0',
                                'pulang_sembuh'          => 'background:#F0FFF4;color:#276749',
                                'pulang_atas_permintaan' => 'background:#FFFBEB;color:#B45309',
                                'meninggal'              => 'background:#F9F5F5;color:#6B4C4C',
                                'dirujuk'                => 'background:#FAF5FF;color:#6B21A8',
                                default                  => 'background:#F9F5F5;color:#6B4C4C',
                            };
                            $spLabel = match($statusPulang) {
                                'dirawat'                => 'Masih Dirawat',
                                'pulang_sembuh'          => 'Pulang Sembuh',
                                'pulang_atas_permintaan' => 'Pulang APS',
                                'meninggal'              => 'Meninggal',
                                'dirujuk'                => 'Dirujuk',
                                default                  => ucfirst(str_replace('_', ' ', $statusPulang)),
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="{{ $spStyle }}">
                            {{ $spLabel }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex gap-1 flex-wrap">
                            @if($visit->medicalRecord)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#F0FFF4;color:#276749;">RME</span>
                            @endif
                            @if($visit->diagnoses->count())
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#EBF8FF;color:#2B6CB0;">Diagnosa</span>
                            @endif
                            @if($visit->bill)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#FAF5FF;color:#6B21A8;">Tagihan</span>
                            @endif
                            @if($inpatient?->resume_pulang)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background:#FFFBEB;color:#B45309;">Resume</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('claims.show', $visit->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                               style="background: #7B1D1D;">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                            <a href="{{ route('claims.export-pdf', $visit->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all hover:bg-red-50"
                               style="color: #7B1D1D; border-color: #E8D5D5;">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center">
                        <i class="fa-solid fa-hospital-user text-3xl mb-3 block" style="color: #E8D5D5;"></i>
                        <p class="text-sm font-medium" style="color: #6B4C4C;">Tidak ada data rawat inap{{ ($startDate || $endDate) ? ' pada periode ini' : '' }}.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

</div>
@endsection
