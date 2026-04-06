@extends('layouts.app')

@section('title', 'Detail Klaim — ' . $visit->no_rawat)

@section('breadcrumb')
    <a href="{{ route('berkas-digital.index') }}" class="hover:opacity-70 transition-opacity" style="color: #6B4C4C;">Berkas Digital</a>
    <span style="color: #E8D5D5;">/</span>
    <a href="{{ route('claims.index', $visit->patient_id) }}" class="hover:opacity-70 transition-opacity" style="color: #6B4C4C;">{{ $visit->patient?->nama_lengkap }}</a>
    <span style="color: #E8D5D5;">/</span>
    <span class="font-medium" style="color: #1A0A0A;">{{ $visit->no_rawat }}</span>
@endsection

@section('content')
<div class="fade-in" style="min-width:0; overflow-x:hidden;">

{{-- Header Info Pasien --}}
<div class="bg-white rounded-2xl p-5 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg font-bold flex-shrink-0"
                 style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                {{ strtoupper(substr($visit->patient?->nama_lengkap ?? 'P', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-base font-bold" style="color: #1A0A0A;">{{ $visit->patient?->nama_lengkap ?? '-' }}</h2>
                <div class="flex items-center gap-3 mt-0.5 flex-wrap text-xs" style="color: #6B4C4C;">
                    <span class="font-mono">RM: {{ $visit->patient?->no_rm ?? '-' }}</span>
                    <span>|</span>
                    <span class="font-mono">BPJS: {{ $visit->patient?->no_bpjs ?? '-' }}</span>
                    <span>|</span>
                    <span>{{ $visit->tanggal_kunjungan?->format('d/m/Y') ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('claims.export-pdf', $visit->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
               style="background: linear-gradient(135deg, #7B1D1D, #5C1414); box-shadow: 0 2px 8px rgba(123,29,29,0.3);">
                <i class="fa-solid fa-file-pdf"></i> Ekspor PDF
            </a>
            <button type="button" onclick="toggleDraft()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border transition-all hover:bg-red-50"
                    style="color: #7B1D1D; border-color: #E8D5D5;">
                <i class="fa-solid fa-file-contract"></i> Buat Draft Klaim
            </button>
            <a href="{{ route('claims.index', $visit->patient_id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium border transition-all hover:bg-gray-50"
               style="color: #6B4C4C; border-color: #E8D5D5;">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

{{-- Info Kunjungan --}}
<div class="bg-white rounded-2xl p-5 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-hospital-user"></i> Informasi Kunjungan
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <p class="text-xs font-semibold mb-0.5" style="color: #6B4C4C;">No. Rawat</p>
            <p class="text-sm font-mono font-semibold" style="color: #1A0A0A;">{{ $visit->no_rawat }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold mb-0.5" style="color: #6B4C4C;">No. SEP</p>
            <p class="text-sm font-mono" style="color: #1A0A0A;">{{ $visit->no_sep ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold mb-0.5" style="color: #6B4C4C;">Poli</p>
            <p class="text-sm" style="color: #1A0A0A;">{{ $visit->poli?->nama_poli ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold mb-0.5" style="color: #6B4C4C;">Dokter</p>
            <p class="text-sm" style="color: #1A0A0A;">{{ $visit->doctor?->nama_dokter ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold mb-0.5" style="color: #6B4C4C;">Tanggal Kunjungan</p>
            <p class="text-sm" style="color: #1A0A0A;">{{ $visit->tanggal_kunjungan?->format('d/m/Y') ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold mb-0.5" style="color: #6B4C4C;">Jenis Penjamin</p>
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
        </div>
    </div>
</div>

{{-- Diagnosa ICD-10 --}}
<div class="bg-white rounded-2xl p-5 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-stethoscope"></i> Diagnosa ICD-10
    </h3>
    @forelse($visit->diagnoses as $d)
    <div class="flex items-center gap-3 py-2.5 border-b last:border-0" style="border-color: #F0E8E8;">
        @if($d->is_primary)
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold flex-shrink-0" style="background:#FFF5F5;color:#7B1D1D;border:1px solid #E8D5D5;">Utama</span>
        @else
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold flex-shrink-0" style="background:#F9F5F5;color:#6B4C4C;border:1px solid #E8D5D5;">Sekunder</span>
        @endif
        <span class="font-mono text-sm font-semibold" style="color: #7B1D1D;">{{ $d->icd10_code }}</span>
        <span class="text-sm" style="color: #1A0A0A;">— {{ $d->icd10Code?->nama_penyakit ?? $d->icd10Code?->deskripsi ?? '-' }}</span>
    </div>
    @empty
    <p class="text-sm py-4 text-center" style="color: #6B4C4C;">Tidak ada diagnosa tercatat.</p>
    @endforelse
</div>

{{-- Rincian Tagihan --}}
@if($visit->bill)
<div class="bg-white rounded-2xl overflow-hidden mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
    <div class="px-5 py-3.5 border-b" style="background: #F9F5F5; border-color: #E8D5D5;">
        <span class="text-sm font-semibold flex items-center gap-2" style="color: #1A0A0A;">
            <i class="fa-solid fa-file-invoice-dollar" style="color: #7B1D1D;"></i>
            Rincian Tagihan
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background: #F9F5F5;">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Item</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Harga</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Qty</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #6B4C4C;">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: #E8D5D5;">
                @foreach($visit->bill->items as $item)
                <tr class="hover:bg-red-50 transition-colors">
                    <td class="px-5 py-3.5" style="color: #1A0A0A;">{{ $item->item_name }}</td>
                    <td class="px-5 py-3.5 text-right font-mono text-xs" style="color: #6B4C4C;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5 text-right" style="color: #1A0A0A;">{{ $item->quantity }}</td>
                    <td class="px-5 py-3.5 text-right font-mono text-xs font-semibold" style="color: #1A0A0A;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="background: #F9F5F5; border-top: 2px solid #E8D5D5;">
                <tr>
                    <td colspan="3" class="px-5 py-3 text-right text-sm font-semibold" style="color: #6B4C4C;">Total</td>
                    <td class="px-5 py-3 text-right font-mono font-bold" style="color: #7B1D1D;">Rp {{ number_format($visit->bill->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

{{-- Draft Klaim (collapsible) --}}
<div id="draft-section" class="hidden">
    <div class="bg-white rounded-2xl p-5 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold flex items-center gap-2" style="color: #7B1D1D;">
                <i class="fa-solid fa-file-contract"></i> Draft Klaim BPJS
            </h3>
            <button type="button" onclick="toggleDraft()"
                    class="text-xs px-3 py-1.5 rounded-lg border"
                    style="color: #6B4C4C; border-color: #E8D5D5;">
                <i class="fa-solid fa-xmark mr-1"></i> Tutup
            </button>
        </div>
        <div id="draft-loading" class="py-6 text-center hidden">
            <i class="fa-solid fa-spinner fa-spin text-2xl" style="color: #7B1D1D;"></i>
            <p class="text-sm mt-2" style="color: #6B4C4C;">Memuat draft...</p>
        </div>
        <pre id="draft-json" class="text-xs rounded-xl p-4 overflow-auto max-h-80 hidden"
             style="background: #F9F5F5; color: #1A0A0A; border: 1px solid #E8D5D5;"></pre>
    </div>
</div>

</div>

@push('scripts')
<script>
var draftLoaded = false;

function toggleDraft() {
    var section = document.getElementById('draft-section');
    var isHidden = section.classList.contains('hidden');
    section.classList.toggle('hidden');

    if (isHidden && !draftLoaded) {
        draftLoaded = true;
        document.getElementById('draft-loading').classList.remove('hidden');
        document.getElementById('draft-json').classList.add('hidden');

        fetch('{{ route("claims.draft", $visit->id) }}')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                document.getElementById('draft-loading').classList.add('hidden');
                var pre = document.getElementById('draft-json');
                pre.textContent = JSON.stringify(data.data, null, 2);
                pre.classList.remove('hidden');
            })
            .catch(function() {
                document.getElementById('draft-loading').classList.add('hidden');
                document.getElementById('draft-json').textContent = 'Gagal memuat draft klaim.';
                document.getElementById('draft-json').classList.remove('hidden');
            });
    }
}
</script>
@endpush
@endsection
