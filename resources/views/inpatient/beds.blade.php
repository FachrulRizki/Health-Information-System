@extends('layouts.app')

@section('title', 'Peta Bed')

@section('breadcrumb')
    <a href="{{ route('inpatient.index') }}" class="hover:text-blue-600" style="color: #64748B;">Rawat Inap</a>
    <i class="fa-solid fa-chevron-right text-xs mx-2" style="color: #cbd5e1;"></i>
    <span class="font-medium" style="color: #0F172A;">Peta Bed</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold" style="color: #0F172A;">Peta Bed Rawat Inap</h2>
        <p class="text-sm mt-0.5" style="color: #64748B;">Status ketersediaan bed secara real-time</p>
    </div>
    <a href="{{ route('inpatient.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium border border-slate-200 hover:bg-slate-50 transition-colors"
       style="color: #64748B;">
        <i class="fa-solid fa-arrow-left"></i> Daftar Pasien
    </a>
</div>

{{-- Legend --}}
<div class="flex flex-wrap gap-3 mb-6">
    @foreach([
        ['#ECFDF5','#10B981','#065F46', 'Tersedia'],
        ['#FEF2F2','#EF4444','#B91C1C', 'Terisi'],
        ['#FFFBEB','#F59E0B','#92400E', 'Dalam Perawatan'],
        ['#F8FAFC','#94a3b8','#475569', 'Tidak Aktif'],
    ] as [$bg, $border, $text, $label])
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border text-xs font-medium"
             style="background: {{ $bg }}; border-color: {{ $border }}; color: {{ $text }};">
            <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $border }};"></span>
            {{ $label }}
        </div>
    @endforeach
</div>

@forelse($rooms as $room)
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-5" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-3" style="background: #F8FAFC;">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #EFF6FF;">
                <i class="fa-solid fa-door-open text-sm" style="color: #2563EB;"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold" style="color: #0F172A;">{{ $room->nama_kamar }}</h2>
                <p class="text-xs" style="color: #64748B;">Kelas {{ $room->kelas }}</p>
            </div>
            <div class="ml-auto flex gap-2">
                @php
                    $available = $room->beds->whereIn('status', ['tersedia', 'available'])->count();
                    $total = $room->beds->count();
                @endphp
                <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #ECFDF5; color: #065F46;">
                    {{ $available }} tersedia
                </span>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium" style="background: #F8FAFC; color: #64748B;">
                    {{ $total }} total
                </span>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($room->beds as $bed)
                    @php
                        $bedStyle = match($bed->status) {
                            'tersedia', 'available'           => 'background:#ECFDF5;border-color:#6EE7B7;color:#065F46',
                            'terisi', 'occupied'              => 'background:#FEF2F2;border-color:#FCA5A5;color:#B91C1C',
                            'dalam_perawatan', 'maintenance'  => 'background:#FFFBEB;border-color:#FCD34D;color:#92400E',
                            default                           => 'background:#F8FAFC;border-color:#CBD5E1;color:#475569',
                        };
                        $bedIcon = match($bed->status) {
                            'tersedia', 'available'           => 'fa-bed text-emerald-500',
                            'terisi', 'occupied'              => 'fa-bed-pulse text-red-500',
                            'dalam_perawatan', 'maintenance'  => 'fa-bed text-amber-500',
                            default                           => 'fa-bed text-slate-400',
                        };
                    @endphp
                    <div class="bed-card border-2 rounded-xl p-3 text-center transition-all hover:scale-[1.02]"
                         style="{{ $bedStyle }}" data-bed-id="{{ $bed->id }}">
                        <i class="fa-solid {{ $bedIcon }} text-lg mb-1 block"></i>
                        <div class="font-bold text-sm">{{ $bed->kode_bed }}</div>
                        <div class="text-xs mt-0.5 capitalize">{{ str_replace('_', ' ', $bed->status) }}</div>
                        @if($bed->currentPatient)
                            <div class="text-xs mt-1 font-medium truncate" title="{{ $bed->currentPatient->nama_lengkap }}">
                                {{ $bed->currentPatient->nama_lengkap }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@empty
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <i class="fa-solid fa-bed text-3xl mb-3 block" style="color: #cbd5e1;"></i>
        <p class="text-sm font-medium" style="color: #64748B;">Tidak ada data kamar aktif</p>
    </div>
@endforelse

@push('scripts')
<script>
function updateBedCard(data) {
    const card = document.querySelector(`.bed-card[data-bed-id="${data.bed_id}"]`);
    if (!card) return;
    const styleMap = {
        tersedia:        'background:#ECFDF5;border-color:#6EE7B7;color:#065F46',
        available:       'background:#ECFDF5;border-color:#6EE7B7;color:#065F46',
        terisi:          'background:#FEF2F2;border-color:#FCA5A5;color:#B91C1C',
        occupied:        'background:#FEF2F2;border-color:#FCA5A5;color:#B91C1C',
        dalam_perawatan: 'background:#FFFBEB;border-color:#FCD34D;color:#92400E',
        maintenance:     'background:#FFFBEB;border-color:#FCD34D;color:#92400E',
    };
    const iconMap = {
        tersedia:        'fa-bed text-emerald-500',
        available:       'fa-bed text-emerald-500',
        terisi:          'fa-bed-pulse text-red-500',
        occupied:        'fa-bed-pulse text-red-500',
        dalam_perawatan: 'fa-bed text-amber-500',
        maintenance:     'fa-bed text-amber-500',
    };
    const style = styleMap[data.status] || 'background:#F8FAFC;border-color:#CBD5E1;color:#475569';
    const icon  = iconMap[data.status]  || 'fa-bed text-slate-400';
    card.style.cssText = style;
    card.innerHTML = `<i class="fa-solid ${icon} text-lg mb-1 block"></i><div class="font-bold text-sm">${data.kode_bed}</div><div class="text-xs mt-0.5 capitalize">${data.status.replace(/_/g,' ')}</div>${data.patient_name ? `<div class="text-xs mt-1 font-medium truncate">${data.patient_name}</div>` : ''}`;
}

if (typeof window.Echo !== 'undefined') {
    window.Echo.channel('beds').listen('BedStatusUpdated', updateBedCard);
} else {
    setInterval(() => location.reload(), 15000);
}
</script>
@endpush
@endsection
