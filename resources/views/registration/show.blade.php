@extends('layouts.app')

@section('title', 'Detail Pasien — ' . $patient->nama_lengkap)

@section('breadcrumb')
    <a href="{{ route('registration.index') }}" class="hover:opacity-70 transition-opacity" style="color: #6B4C4C;">Pendaftaran</a>
    <span style="color: #E8D5D5;">/</span>
    <span class="font-medium" style="color: #1A0A0A;">{{ $patient->nama_lengkap }}</span>
@endsection

@section('content')
<div class="fade-in">

@if(session('success'))
<div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="background: #F0FFF4; border-color: #9AE6B4;">
    <i class="fa-solid fa-circle-check flex-shrink-0" style="color: #276749;"></i>
    <span class="text-sm font-medium" style="color: #276749;">{{ session('success') }}</span>
</div>
@endif

{{-- Header Card --}}
<div class="bg-white rounded-2xl p-6 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 4px 16px rgba(123,29,29,0.08);">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-5">
            {{-- Avatar --}}
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-white text-3xl font-bold flex-shrink-0"
                 style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C); box-shadow: 0 4px 12px rgba(123,29,29,0.3);">
                {{ strtoupper(substr($patient->nama_lengkap ?? 'P', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold" style="color: #1A0A0A;">{{ $patient->nama_lengkap }}</h2>
                <p class="text-sm font-mono mt-1" style="color: #7B1D1D;">No. RM: {{ $patient->no_rm }}</p>
                <div class="flex items-center gap-3 mt-2 flex-wrap">
                    <span class="text-sm" style="color: #6B4C4C;">
                        <i class="fa-solid fa-cake-candles mr-1"></i>
                        {{ $patient->tanggal_lahir?->format('d F Y') ?? '-' }}
                        @if($patient->tanggal_lahir)
                            <span class="ml-1 text-xs">({{ $patient->tanggal_lahir->age }} thn)</span>
                        @endif
                    </span>
                    <span class="text-sm" style="color: #6B4C4C;">
                        <i class="fa-solid fa-venus-mars mr-1"></i>
                        {{ $patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                    @php
                        $pStyle = match($patient->jenis_penjamin) {
                            'bpjs'     => 'background:#EBF8FF;color:#2B6CB0',
                            'asuransi' => 'background:#FAF5FF;color:#6B21A8',
                            default    => 'background:#F9F5F5;color:#6B4C4C',
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $pStyle }}">
                        {{ strtoupper($patient->jenis_penjamin) }}
                    </span>
                </div>
            </div>
        </div>
        <a href="{{ route('registration.create-visit', $patient->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90 flex-shrink-0"
           style="background: linear-gradient(135deg, #7B1D1D, #5C1414); box-shadow: 0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-calendar-plus"></i>
            Buat Kunjungan Baru
        </a>
    </div>
</div>

{{-- Data Identitas --}}
<div class="bg-white rounded-2xl p-6 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-id-card"></i> Data Identitas
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1rem;">
        @php
            $fields = [
                ['label' => 'No. Rekam Medis',  'value' => $patient->no_rm,                          'icon' => 'fa-hashtag'],
                ['label' => 'Nama Lengkap',      'value' => $patient->nama_lengkap,                   'icon' => 'fa-user'],
                ['label' => 'Tanggal Lahir',     'value' => $patient->tanggal_lahir?->format('d F Y'),'icon' => 'fa-calendar'],
                ['label' => 'Jenis Kelamin',     'value' => $patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan', 'icon' => 'fa-venus-mars'],
                ['label' => 'NIK',               'value' => $patient->nik ?? '-',                     'icon' => 'fa-id-badge'],
                ['label' => 'No. Telepon',       'value' => $patient->no_telepon ?? '-',              'icon' => 'fa-phone'],
                ['label' => 'Alamat',            'value' => $patient->alamat ?? '-',                  'icon' => 'fa-map-marker-alt'],
                ['label' => 'Jenis Penjamin',    'value' => strtoupper($patient->jenis_penjamin),     'icon' => 'fa-shield-halved'],
                ['label' => 'No. BPJS',          'value' => $patient->no_bpjs ?? '-',                 'icon' => 'fa-credit-card'],
                ['label' => 'No. Polis Asuransi','value' => $patient->no_polis_asuransi ?? '-',       'icon' => 'fa-file-contract'],
                ['label' => 'Nama Asuransi',     'value' => $patient->nama_asuransi ?? '-',           'icon' => 'fa-building'],
            ];
        @endphp
        @foreach($fields as $field)
        <div class="flex items-start gap-3 p-3 rounded-xl" style="background: #F9F5F5; border: 1px solid #F0E8E8;">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background: #FFF5F5; color: #7B1D1D;">
                <i class="fa-solid {{ $field['icon'] }} text-xs"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-medium mb-0.5" style="color: #6B4C4C;">{{ $field['label'] }}</p>
                <p class="text-sm font-semibold truncate" style="color: #1A0A0A;">{{ $field['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Riwayat Kunjungan --}}
<div class="bg-white rounded-2xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background: #F9F5F5; border-color: #E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
            <i class="fa-solid fa-clock-rotate-left" style="color: #7B1D1D;"></i>
            Riwayat Kunjungan
        </span>
        <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #FFF5F5; color: #7B1D1D;">
            {{ $visits->total() }} kunjungan
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
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">RME</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #E8D5D5;">
                @forelse($visits as $visit)
                @php
                    $statusStyle = match($visit->status) {
                        'menunggu','pendaftaran' => 'background:#FEF9C3;color:#854D0E',
                        'dipanggil','dalam_pemeriksaan' => 'background:#DBEAFE;color:#1D4ED8',
                        'farmasi' => 'background:#EDE9FE;color:#6D28D9',
                        'kasir'   => 'background:#DCFCE7;color:#166534',
                        'selesai' => 'background:#374151;color:#F9FAFB',
                        'batal'   => 'background:#FEE2E2;color:#991B1B',
                        default   => 'background:#F9F5F5;color:#6B4C4C',
                    };
                @endphp
                <tr class="hover:bg-red-50 transition-colors">
                    <td class="px-5 py-3.5 font-mono text-xs" style="color: #6B4C4C;">{{ $visit->no_rawat }}</td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">{{ $visit->tanggal_kunjungan?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">{{ $visit->poli?->nama_poli ?? '-' }}</td>
                    <td class="px-5 py-3.5 text-sm" style="color: #1A0A0A;">{{ $visit->doctor?->nama_dokter ?? '-' }}</td>
                    <td class="px-5 py-3.5">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $statusStyle }}">
                            {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($visit->medicalRecord)
                        <a href="{{ route('rme.show', $visit->id) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:opacity-90"
                           style="background: #7B1D1D;">
                            <i class="fa-solid fa-file-medical"></i> Lihat
                        </a>
                        @else
                        <span class="text-xs" style="color: #6B4C4C;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <i class="fa-solid fa-calendar-xmark text-3xl mb-3 block" style="color: #E8D5D5;"></i>
                        <p class="text-sm font-medium" style="color: #6B4C4C;">Belum ada riwayat kunjungan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($visits->hasPages())
    <div class="px-5 py-4 border-t" style="border-color: #E8D5D5;">
        {{ $visits->links() }}
    </div>
    @endif
</div>

</div>
@endsection
