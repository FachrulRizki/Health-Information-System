@extends('layouts.app')

@section('title', 'Farmasi — Daftar Resep')

@section('breadcrumb')
    <span class="font-medium" style="color: #0F172A;">Farmasi</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold" style="color: #0F172A;">Farmasi — Daftar Resep Masuk</h2>
        <p class="text-sm mt-0.5" style="color: #64748B;">Kelola dan proses resep dari dokter</p>
    </div>
    <a href="{{ route('pharmacy.stock') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:scale-[1.02]"
       style="background: linear-gradient(135deg, #7C3AED, #6D28D9);">
        <i class="fa-solid fa-boxes-stacked"></i>
        Stok Obat
    </a>
</div>

@foreach(['success','warning','error'] as $type)
    @if(session($type))
        @php
            $alertStyle = match($type) {
                'success' => 'background:#ECFDF5;border-color:#A7F3D0;color:#065F46',
                'warning' => 'background:#FFFBEB;border-color:#FDE68A;color:#92400E',
                default   => 'background:#FEF2F2;border-color:#FECACA;color:#B91C1C',
            };
            $alertIcon = match($type) {
                'success' => 'fa-circle-check',
                'warning' => 'fa-triangle-exclamation',
                default   => 'fa-circle-exclamation',
            };
        @endphp
        <div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="{{ $alertStyle }}">
            <i class="fa-solid {{ $alertIcon }} flex-shrink-0"></i>
            <span class="text-sm font-medium">{{ session($type) }}</span>
        </div>
    @endif
@endforeach

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
    <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between" style="background: #F8FAFC;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #0F172A;">
            <i class="fa-solid fa-prescription" style="color: #2563EB;"></i>
            Resep Masuk
        </span>
        <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #EFF6FF; color: #2563EB;">
            {{ $prescriptions->count() }} resep
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F8FAFC;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Pasien</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">No. Rawat</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Tipe Resep</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Waktu</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($prescriptions as $prescription)
                    @php
                        $statusStyle = match($prescription->status) {
                            'pending'   => 'background:#FFFBEB;color:#92400E',
                            'validated' => 'background:#EFF6FF;color:#1D4ED8',
                            'dispensed' => 'background:#ECFDF5;color:#065F46',
                            'cancelled' => 'background:#FEF2F2;color:#B91C1C',
                            default     => 'background:#F8FAFC;color:#475569',
                        };
                        $statusLabel = match($prescription->status) {
                            'pending'   => 'Pending',
                            'validated' => 'Tervalidasi',
                            'dispensed' => 'Diserahkan',
                            'cancelled' => 'Dibatalkan',
                            default     => $prescription->status,
                        };
                        $typeMap = ['dokter'=>'Resep Dokter','terjadwal'=>'Obat Terjadwal','pulang'=>'Resep Pulang'];
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                     style="background: linear-gradient(135deg, #7C3AED, #6D28D9);">
                                    {{ strtoupper(substr($prescription->visit?->patient?->nama_lengkap ?? 'P', 0, 1)) }}
                                </div>
                                <span class="font-medium" style="color: #0F172A;">{{ $prescription->visit?->patient?->nama_lengkap ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-xs" style="color: #64748B;">{{ $prescription->visit?->no_rawat ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-sm" style="color: #0F172A;">{{ $typeMap[$prescription->type] ?? $prescription->type }}</td>
                        <td class="px-5 py-3.5 text-xs" style="color: #64748B;">{{ $prescription->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $statusStyle }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <a href="{{ route('pharmacy.show', $prescription->id) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white transition-all hover:scale-[1.03]"
                               style="background: #2563EB;">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <i class="fa-solid fa-prescription-bottle text-3xl mb-3 block" style="color: #cbd5e1;"></i>
                            <p class="text-sm font-medium" style="color: #64748B;">Tidak ada resep masuk</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
