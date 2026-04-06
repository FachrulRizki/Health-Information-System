@extends('layouts.app')

@section('title', 'Farmasi — Detail Resep')

@section('breadcrumb')
    <a href="{{ route('pharmacy.index') }}" class="hover:text-blue-600" style="color: #64748B;">Farmasi</a>
    <i class="fa-solid fa-chevron-right text-xs mx-2" style="color: #cbd5e1;"></i>
    <span class="font-medium" style="color: #0F172A;">Detail Resep</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pharmacy.index') }}"
           class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors"
           style="color: #64748B;">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <h2 class="text-xl font-bold" style="color: #0F172A;">Detail Resep</h2>
    </div>

    @foreach(['success','warning','error'] as $type)
        @if(session($type))
            @php
                $alertStyle = match($type) {
                    'success' => 'background:#ECFDF5;border-color:#A7F3D0;color:#065F46',
                    'warning' => 'background:#FFFBEB;border-color:#FDE68A;color:#92400E',
                    default   => 'background:#FEF2F2;border-color:#FECACA;color:#B91C1C',
                };
                $alertIcon = match($type) { 'success' => 'fa-circle-check', 'warning' => 'fa-triangle-exclamation', default => 'fa-circle-exclamation' };
            @endphp
            <div class="flex items-center gap-3 rounded-xl border p-4 mb-5" style="{{ $alertStyle }}">
                <i class="fa-solid {{ $alertIcon }} flex-shrink-0"></i>
                <span class="text-sm font-medium">{{ session($type) }}</span>
            </div>
        @endif
    @endforeach

    {{-- Info Resep --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-5 grid grid-cols-2 gap-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div>
            <p class="text-xs font-medium mb-0.5" style="color: #64748B;">No. Rawat</p>
            <p class="font-mono font-semibold text-sm" style="color: #0F172A;">{{ $prescription->visit?->no_rawat ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-medium mb-0.5" style="color: #64748B;">Pasien</p>
            <p class="font-semibold text-sm" style="color: #0F172A;">{{ $prescription->visit?->patient?->nama_lengkap ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-medium mb-0.5" style="color: #64748B;">Tipe Resep</p>
            <p class="text-sm" style="color: #0F172A;">{{ ['dokter'=>'Resep Dokter','terjadwal'=>'Obat Terjadwal','pulang'=>'Resep Pulang'][$prescription->type] ?? $prescription->type }}</p>
        </div>
        <div>
            <p class="text-xs font-medium mb-0.5" style="color: #64748B;">Status</p>
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
            @endphp
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="{{ $statusStyle }}">{{ $statusLabel }}</span>
        </div>
    </div>

    {{-- Item Resep --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-5" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-2" style="background: #F8FAFC;">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #EFF6FF;">
                <i class="fa-solid fa-pills text-xs" style="color: #2563EB;"></i>
            </div>
            <h3 class="text-sm font-semibold" style="color: #0F172A;">Item Resep</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background: #F8FAFC;">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Obat</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Jumlah</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Stok Tersedia</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Dosis</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #64748B;">Status Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($prescription->items as $item)
                        @php
                            $vItem = collect($validationResult['items'])->firstWhere('prescription_item_id', $item->id);
                            $sufficient = $vItem['is_sufficient'] ?? true;
                        @endphp
                        <tr class="{{ $sufficient ? 'hover:bg-slate-50' : 'bg-red-50' }} transition-colors">
                            <td class="px-5 py-3.5 font-medium" style="color: #0F172A;">{{ $item->drug?->nama ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-right font-mono" style="color: #0F172A;">{{ $item->quantity }}</td>
                            <td class="px-5 py-3.5 text-right font-mono font-semibold" style="color: {{ $sufficient ? '#10B981' : '#EF4444' }};">
                                {{ $vItem['available_quantity'] ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-sm" style="color: #64748B;">{{ $item->dosage ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                                      style="{{ $sufficient ? 'background:#ECFDF5;color:#065F46' : 'background:#FEF2F2;color:#B91C1C' }}">
                                    {{ $sufficient ? 'Cukup' : 'Tidak Cukup' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Actions --}}
    @if($prescription->status === 'pending')
        <form method="POST" action="{{ route('pharmacy.validate', $prescription->id) }}" class="mb-5">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:scale-[1.02]"
                    style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
                <i class="fa-solid fa-circle-check"></i> Validasi Resep
            </button>
        </form>
    @elseif($prescription->status === 'validated')
        <form method="POST" action="{{ route('pharmacy.dispense', $prescription->id) }}" class="mb-5"
              onsubmit="return confirm('Serahkan semua obat kepada pasien?')">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:scale-[1.02]"
                    style="background: linear-gradient(135deg, #10B981, #059669);">
                <i class="fa-solid fa-hand-holding-medical"></i> Serahkan Obat
            </button>
        </form>
    @endif

    {{-- SOAP Farmasi --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden" style="box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-2" style="background: #F8FAFC;">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #F5F3FF;">
                <i class="fa-solid fa-comment-medical text-xs" style="color: #7C3AED;"></i>
            </div>
            <h3 class="text-sm font-semibold" style="color: #0F172A;">SOAP Farmasi (Konseling Obat)</h3>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('pharmacy.soap', $prescription->id) }}">
                @csrf
                <textarea name="pharmacy_notes" rows="4"
                          placeholder="Catatan konseling obat, instruksi penggunaan, efek samping yang perlu diperhatikan..."
                          class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:border-blue-400 transition-all"
                          style="color: #0F172A;">{{ old('pharmacy_notes', $prescription->pharmacy_notes) }}</textarea>
                <button type="submit"
                        class="mt-3 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:scale-[1.02]"
                        style="background: linear-gradient(135deg, #7C3AED, #6D28D9);">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Catatan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
