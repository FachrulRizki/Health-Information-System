@extends('layouts.app')

@section('title', 'Input Hasil Lab')

@section('breadcrumb')
    <a href="{{ route('lab.index') }}" class="transition-opacity hover:opacity-70" style="color: #6B4C4C;">Laboratorium</a>
    <span style="color: #E8D5D5;">/</span>
    <span class="font-medium" style="color: #7B1D1D;">Input Hasil</span>
@endsection

@section('content')
<div class="fade-in max-w-3xl mx-auto">

    {{-- Back button --}}
    <a href="{{ route('lab.index') }}"
       class="inline-flex items-center gap-2 text-sm mb-5 transition-opacity hover:opacity-70"
       style="color: #7B1D1D;">
        <i class="fa-solid fa-arrow-left"></i>
        Kembali ke Daftar
    </a>

    {{-- Patient info card --}}
    <div class="bg-white rounded-xl border p-5 mb-4" style="border-color: #E8D5D5; box-shadow: 0 1px 3px rgba(123,29,29,0.06);">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                 style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                {{ strtoupper(substr($labRequest->visit?->patient?->nama_lengkap ?? 'P', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-base" style="color: #1A0A0A;">
                    {{ $labRequest->visit?->patient?->nama_lengkap ?? '-' }}
                </h3>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-xs" style="color: #6B4C4C;">
                    <span><i class="fa-solid fa-id-card mr-1"></i>{{ $labRequest->visit?->patient?->no_rm ?? '-' }}</span>
                    <span><i class="fa-solid fa-hashtag mr-1"></i>No. Rawat: {{ $labRequest->visit?->no_rawat ?? '-' }}</span>
                    @if($labRequest->visit?->poli)
                        <span><i class="fa-solid fa-hospital mr-1"></i>{{ $labRequest->visit->poli->nama_poli }}</span>
                    @endif
                </div>
            </div>
            @php
                $statusStyle = match($labRequest->status) {
                    'completed'   => 'background:#F0FFF4;color:#276749;border:1px solid #9AE6B4',
                    'in_progress' => 'background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE',
                    default       => 'background:#FFFBEB;color:#B7791F;border:1px solid #FDE68A',
                };
                $statusLabel = match($labRequest->status) {
                    'completed'   => 'Selesai',
                    'in_progress' => 'Diproses',
                    default       => 'Pending',
                };
            @endphp
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold flex-shrink-0" style="{{ $statusStyle }}">
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    {{-- Request info --}}
    <div class="bg-white rounded-xl border p-5 mb-4" style="border-color: #E8D5D5; box-shadow: 0 1px 3px rgba(123,29,29,0.06);">
        <h4 class="text-sm font-semibold mb-3 flex items-center gap-2" style="color: #7B1D1D;">
            <i class="fa-solid fa-flask"></i>
            Detail Permintaan
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
                <p class="text-xs font-medium mb-0.5" style="color: #6B4C4C;">Jenis Pemeriksaan</p>
                <p class="font-semibold" style="color: #1A0A0A;">{{ $labRequest->examinationType?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium mb-0.5" style="color: #6B4C4C;">Dokter Peminta</p>
                <p class="font-semibold" style="color: #1A0A0A;">{{ $labRequest->visit?->doctor?->nama_dokter ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium mb-0.5" style="color: #6B4C4C;">Waktu Permintaan</p>
                <p class="font-semibold" style="color: #1A0A0A;">{{ $labRequest->created_at?->format('d M Y, H:i') ?? '-' }}</p>
            </div>
            @if($labRequest->sample_taken_at)
            <div>
                <p class="text-xs font-medium mb-0.5" style="color: #6B4C4C;">Sampel Diambil</p>
                <p class="font-semibold" style="color: #1A0A0A;">{{ $labRequest->sample_taken_at->format('d M Y, H:i') }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Existing result (if any) --}}
    @if($labRequest->result)
        <div class="rounded-xl border p-5 mb-4" style="background: #F0FFF4; border-color: #9AE6B4;">
            <h4 class="text-sm font-semibold mb-3 flex items-center gap-2" style="color: #276749;">
                <i class="fa-solid fa-circle-check"></i>
                Hasil Sebelumnya
            </h4>
            <div class="rounded-lg p-4 text-sm font-mono whitespace-pre-wrap" style="background: #fff; border: 1px solid #9AE6B4; color: #1A0A0A; line-height: 1.6;">{{ is_array($labRequest->result->result_data) ? json_encode($labRequest->result->result_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $labRequest->result->result_data }}</div>
            <p class="text-xs mt-2" style="color: #276749;">
                <i class="fa-solid fa-clock mr-1"></i>
                Diinput: {{ $labRequest->result->created_at?->format('d M Y, H:i') }}
            </p>
        </div>
    @endif

    {{-- Form input hasil --}}
    <div class="bg-white rounded-xl border p-5" style="border-color: #E8D5D5; box-shadow: 0 1px 3px rgba(123,29,29,0.06);">
        <h4 class="text-sm font-semibold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
            <i class="fa-solid fa-pen-to-square"></i>
            {{ $labRequest->result ? 'Edit Hasil Laboratorium' : 'Input Hasil Laboratorium' }}
        </h4>

        @if($errors->any())
            <div class="rounded-xl border p-4 mb-4" style="background: #FFF5F5; border-color: #FEB2B2;">
                <ul class="text-sm space-y-1" style="color: #C53030;">
                    @foreach($errors->all() as $error)
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-exclamation text-xs"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('lab.store-result', $labRequest->id) }}">
            @csrf
            <div class="mb-4">
                <label for="result_data" class="block text-sm font-medium mb-1.5" style="color: #1A0A0A;">
                    Hasil Pemeriksaan <span style="color: #C53030;">*</span>
                </label>
                <textarea id="result_data" name="result_data" rows="10"
                          class="w-full rounded-xl border px-4 py-3 text-sm font-mono resize-y transition-all outline-none"
                          style="border-color: #E8D5D5; color: #1A0A0A; background: #FAFAFA; line-height: 1.6;"
                          onfocus="this.style.borderColor='#7B1D1D'; this.style.boxShadow='0 0 0 3px rgba(123,29,29,0.1)'"
                          onblur="this.style.borderColor='#E8D5D5'; this.style.boxShadow='none'"
                          placeholder="Masukkan hasil pemeriksaan laboratorium...&#10;Contoh:&#10;Hemoglobin: 13.5 g/dL (Normal: 12-16)&#10;Leukosit: 8.500 /µL (Normal: 4.000-11.000)&#10;Trombosit: 250.000 /µL (Normal: 150.000-400.000)"
                          required>{{ old('result_data', $labRequest->result?->result_data ? (is_array($labRequest->result->result_data) ? json_encode($labRequest->result->result_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $labRequest->result->result_data) : '') }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('lab.index') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all"
                   style="color: #6B4C4C; border: 1px solid #E8D5D5;"
                   onmouseover="this.style.background='#F9F5F5'"
                   onmouseout="this.style.background='transparent'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                        style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Hasil
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
