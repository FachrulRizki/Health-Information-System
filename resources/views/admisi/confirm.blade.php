@extends('layouts.app')

@section('title', 'Konfirmasi Admisi')

@section('breadcrumb')
    <a href="{{ route('admisi.index') }}" class="hover:opacity-70 transition-opacity" style="color: #6B4C4C;">Admisi</a>
    <span style="color: #E8D5D5;">/</span>
    <span class="font-medium" style="color: #1A0A0A;">Konfirmasi Admisi</span>
@endsection

@section('content')
<div class="fade-in" style="max-width: 900px;">

{{-- Info Pasien --}}
<div class="bg-white rounded-2xl p-5 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 4px 16px rgba(123,29,29,0.08);">
    <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
        <i class="fa-solid fa-user-injured"></i> Informasi Pasien
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
        @foreach([
            ['label' => 'Nama Pasien',  'value' => $visit->patient?->nama_lengkap ?? '-', 'icon' => 'fa-user'],
            ['label' => 'No. RM',       'value' => $visit->patient?->no_rm ?? '-',         'icon' => 'fa-hashtag'],
            ['label' => 'No. Rawat',    'value' => $visit->no_rawat,                        'icon' => 'fa-file-medical'],
            ['label' => 'Poli',         'value' => $visit->poli?->nama_poli ?? '-',         'icon' => 'fa-hospital'],
            ['label' => 'Dokter',       'value' => $visit->doctor?->nama_dokter ?? '-',     'icon' => 'fa-user-doctor'],
            ['label' => 'Penjamin',     'value' => strtoupper($visit->jenis_penjamin),      'icon' => 'fa-shield-halved'],
        ] as $f)
        <div class="flex items-start gap-3 p-3 rounded-xl" style="background: #F9F5F5; border: 1px solid #F0E8E8;">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background: #FFF5F5; color: #7B1D1D;">
                <i class="fa-solid {{ $f['icon'] }} text-xs"></i>
            </div>
            <div>
                <p class="text-xs" style="color: #6B4C4C;">{{ $f['label'] }}</p>
                <p class="text-sm font-semibold" style="color: #1A0A0A;">{{ $f['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Form Admisi --}}
<form method="POST" action="{{ route('admisi.store', $visit->id) }}">
    @csrf

    {{-- Pilih Kamar / Bed --}}
    <div class="bg-white rounded-2xl p-5 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
        <h3 class="text-sm font-bold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
            <i class="fa-solid fa-bed"></i> Pilih Kamar & Tempat Tidur
        </h3>

        @error('bed_id')
        <div class="flex items-center gap-2 p-3 rounded-xl mb-4" style="background: #FEE2E2; border: 1px solid #FECACA;">
            <i class="fa-solid fa-circle-exclamation text-sm" style="color: #991B1B;"></i>
            <span class="text-sm" style="color: #991B1B;">{{ $message }}</span>
        </div>
        @enderror

        <input type="hidden" name="bed_id" id="selected_bed_id" value="{{ old('bed_id') }}">

        @forelse($rooms as $room)
        @php $availBeds = $room->beds->whereIn('status', ['tersedia', 'available']); @endphp
        @if($availBeds->count() > 0)
        <div class="mb-5">
            <div class="flex items-center gap-2 mb-3">
                <h4 class="text-sm font-semibold" style="color: #1A0A0A;">{{ $room->nama_kamar }}</h4>
                @if($room->kelas)
                <span class="text-xs px-2 py-0.5 rounded-full" style="background: #EBF8FF; color: #2B6CB0;">
                    Kelas {{ $room->kelas }}
                </span>
                @endif
                <span class="text-xs px-2 py-0.5 rounded-full" style="background: #DCFCE7; color: #166534;">
                    {{ $availBeds->count() }} tersedia
                </span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.75rem;">
                @foreach($availBeds as $bed)
                <button type="button"
                        onclick="selectBed({{ $bed->id }}, this)"
                        data-bed-id="{{ $bed->id }}"
                        class="bed-card p-3 rounded-xl border-2 text-left transition-all"
                        style="border-color: #E8D5D5; background: #FFFFFF; cursor: pointer;">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-bed text-sm" style="color: #7B1D1D;"></i>
                        <span class="text-sm font-bold" style="color: #1A0A0A;">{{ $bed->kode_bed }}</span>
                    </div>
                    @if($room->kelas)
                    <p class="text-xs" style="color: #6B4C4C;">Kelas {{ $room->kelas }}</p>
                    @endif
                    <p class="text-xs mt-1 font-medium" style="color: #276749;">Tersedia</p>
                </button>
                @endforeach
            </div>
        </div>
        @endif
        @empty
        <div class="py-8 text-center">
            <i class="fa-solid fa-bed-pulse text-3xl mb-3 block" style="color: #E8D5D5;"></i>
            <p class="text-sm font-medium mb-1" style="color: #6B4C4C;">Tidak ada tempat tidur tersedia saat ini</p>
            <p class="text-xs mb-3" style="color: #9B7B7B;">Pastikan data kamar dan bed sudah diinput di Master Data.</p>
            <a href="{{ route('master.rooms.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white" style="background:#7B1D1D;">
                <i class="fa-solid fa-bed"></i> Kelola Master Kamar
            </a>
        </div>
        @endforelse
    </div>

    {{-- Catatan Admisi --}}
    <div class="bg-white rounded-2xl p-5 mb-5" style="border: 1px solid #E8D5D5; box-shadow: 0 2px 8px rgba(123,29,29,0.06);">
        <h3 class="text-sm font-bold mb-3 flex items-center gap-2" style="color: #7B1D1D;">
            <i class="fa-solid fa-notes-medical"></i> Catatan Admisi
        </h3>
        <textarea name="catatan_admisi" rows="3"
                  placeholder="Catatan tambahan untuk admisi (opsional)..."
                  class="w-full border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 transition-all resize-none"
                  style="border-color: #E8D5D5; color: #1A0A0A; --tw-ring-color: rgba(123,29,29,0.15);">{{ old('catatan_admisi') }}</textarea>
    </div>

    {{-- Tombol Aksi --}}
    <div class="flex items-center gap-3">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                style="background: linear-gradient(135deg, #7B1D1D, #5C1414); box-shadow: 0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-check-circle"></i>
            Konfirmasi Admisi
        </button>
        <a href="{{ route('admisi.index') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium border transition-all hover:opacity-80"
           style="color: #6B4C4C; border-color: #E8D5D5;">
            <i class="fa-solid fa-xmark"></i>
            Batal
        </a>
    </div>
</form>

</div>

@push('scripts')
<script>
function selectBed(bedId, el) {
    document.getElementById('selected_bed_id').value = bedId;
    document.querySelectorAll('.bed-card').forEach(function(card) {
        card.style.borderColor = '#E8D5D5';
        card.style.background  = '#FFFFFF';
    });
    el.style.borderColor = '#7B1D1D';
    el.style.background  = '#FFF5F5';
}
</script>
@endpush
@endsection
