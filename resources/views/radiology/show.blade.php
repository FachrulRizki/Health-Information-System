@extends('layouts.app')

@section('title', 'Input Hasil Radiologi')

@section('breadcrumb')
    <a href="{{ route('radiology.index') }}" class="transition-opacity hover:opacity-70" style="color: #6B4C4C;">Radiologi</a>
    <span style="color: #E8D5D5;">/</span>
    <span class="font-medium" style="color: #7B1D1D;">Input Hasil</span>
@endsection

@section('content')
<div class="fade-in max-w-3xl mx-auto">

    {{-- Back button --}}
    <a href="{{ route('radiology.index') }}"
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
                {{ strtoupper(substr($radiologyRequest->visit?->patient?->nama_lengkap ?? 'P', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-base" style="color: #1A0A0A;">
                    {{ $radiologyRequest->visit?->patient?->nama_lengkap ?? '-' }}
                </h3>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-xs" style="color: #6B4C4C;">
                    <span><i class="fa-solid fa-id-card mr-1"></i>{{ $radiologyRequest->visit?->patient?->no_rm ?? '-' }}</span>
                    <span><i class="fa-solid fa-hashtag mr-1"></i>No. Rawat: {{ $radiologyRequest->visit?->no_rawat ?? '-' }}</span>
                    @if($radiologyRequest->visit?->poli)
                        <span><i class="fa-solid fa-hospital mr-1"></i>{{ $radiologyRequest->visit->poli->nama_poli }}</span>
                    @endif
                </div>
            </div>
            @php
                $statusStyle = match($radiologyRequest->status) {
                    'completed'   => 'background:#F0FFF4;color:#276749;border:1px solid #9AE6B4',
                    'in_progress' => 'background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE',
                    default       => 'background:#FFFBEB;color:#B7791F;border:1px solid #FDE68A',
                };
                $statusLabel = match($radiologyRequest->status) {
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
            <i class="fa-solid fa-x-ray"></i>
            Detail Permintaan
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
                <p class="text-xs font-medium mb-0.5" style="color: #6B4C4C;">Jenis Pemeriksaan</p>
                <p class="font-semibold" style="color: #1A0A0A;">{{ $radiologyRequest->examinationType?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium mb-0.5" style="color: #6B4C4C;">Dokter Peminta</p>
                <p class="font-semibold" style="color: #1A0A0A;">{{ $radiologyRequest->visit?->doctor?->nama_dokter ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium mb-0.5" style="color: #6B4C4C;">Waktu Permintaan</p>
                <p class="font-semibold" style="color: #1A0A0A;">{{ $radiologyRequest->created_at?->format('d M Y, H:i') ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Existing result (if any) --}}
    @if($radiologyRequest->result)
        <div class="rounded-xl border p-5 mb-4" style="background: #F0FFF4; border-color: #9AE6B4;">
            <h4 class="text-sm font-semibold mb-3 flex items-center gap-2" style="color: #276749;">
                <i class="fa-solid fa-circle-check"></i>
                Hasil Sebelumnya
            </h4>
            <div class="rounded-lg p-4 text-sm whitespace-pre-wrap mb-3" style="background: #fff; border: 1px solid #9AE6B4; color: #1A0A0A; line-height: 1.6;">{{ $radiologyRequest->result->result_notes }}</div>

            @if($radiologyRequest->result->file_path)
                <div class="flex items-center gap-3 p-3 rounded-lg" style="background: #fff; border: 1px solid #9AE6B4;">
                    @php
                        $ext = pathinfo($radiologyRequest->result->file_path, PATHINFO_EXTENSION);
                        $isPdf = strtolower($ext) === 'pdf';
                    @endphp
                    <i class="fa-solid {{ $isPdf ? 'fa-file-pdf' : 'fa-file-image' }} text-xl flex-shrink-0" style="color: #276749;"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium" style="color: #276749;">File terlampir</p>
                        <p class="text-xs truncate" style="color: #6B4C4C;">{{ basename($radiologyRequest->result->file_path) }}</p>
                    </div>
                    <a href="{{ asset('storage/' . $radiologyRequest->result->file_path) }}"
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all hover:opacity-90"
                       style="background: #276749; color: #fff;">
                        <i class="fa-solid fa-download"></i>
                        {{ $isPdf ? 'Buka PDF' : 'Lihat Gambar' }}
                    </a>
                </div>
            @endif

            <p class="text-xs mt-2" style="color: #276749;">
                <i class="fa-solid fa-clock mr-1"></i>
                Diinput: {{ $radiologyRequest->result->created_at?->format('d M Y, H:i') }}
            </p>
        </div>
    @endif

    {{-- Form input hasil --}}
    <div class="bg-white rounded-xl border p-5" style="border-color: #E8D5D5; box-shadow: 0 1px 3px rgba(123,29,29,0.06);">
        <h4 class="text-sm font-semibold mb-4 flex items-center gap-2" style="color: #7B1D1D;">
            <i class="fa-solid fa-pen-to-square"></i>
            {{ $radiologyRequest->result ? 'Edit Hasil Radiologi' : 'Input Hasil Radiologi' }}
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

        <form method="POST" action="{{ route('radiology.store-result', $radiologyRequest->id) }}" enctype="multipart/form-data">
            @csrf

            {{-- Catatan hasil --}}
            <div class="mb-4">
                <label for="result_notes" class="block text-sm font-medium mb-1.5" style="color: #1A0A0A;">
                    Catatan Hasil <span style="color: #C53030;">*</span>
                </label>
                <textarea id="result_notes" name="result_notes" rows="8"
                          class="w-full rounded-xl border px-4 py-3 text-sm resize-y transition-all outline-none"
                          style="border-color: #E8D5D5; color: #1A0A0A; background: #FAFAFA; line-height: 1.6;"
                          onfocus="this.style.borderColor='#7B1D1D'; this.style.boxShadow='0 0 0 3px rgba(123,29,29,0.1)'"
                          onblur="this.style.borderColor='#E8D5D5'; this.style.boxShadow='none'"
                          placeholder="Masukkan interpretasi dan temuan hasil radiologi...&#10;Contoh:&#10;Kesan: Tidak tampak kelainan pada foto thorax PA&#10;Cor: Besar dan bentuk normal&#10;Pulmo: Corakan bronkovaskular normal"
                          required>{{ old('result_notes', $radiologyRequest->result?->result_notes ?? '') }}</textarea>
            </div>

            {{-- Upload file --}}
            <div class="mb-5">
                <label for="file" class="block text-sm font-medium mb-1.5" style="color: #1A0A0A;">
                    Upload File <span class="text-xs font-normal" style="color: #6B4C4C;">(opsional — JPG, PNG, PDF, maks. 10MB)</span>
                </label>
                <div id="drop-zone"
                     class="relative rounded-xl border-2 border-dashed p-6 text-center transition-all cursor-pointer"
                     style="border-color: #E8D5D5;"
                     onclick="document.getElementById('file').click()"
                     ondragover="event.preventDefault(); this.style.borderColor='#7B1D1D'; this.style.background='#FDF8F8';"
                     ondragleave="this.style.borderColor='#E8D5D5'; this.style.background='transparent';"
                     ondrop="handleDrop(event)">
                    <i class="fa-solid fa-cloud-arrow-up text-2xl mb-2 block" style="color: #E8D5D5;" id="drop-icon"></i>
                    <p class="text-sm font-medium" style="color: #6B4C4C;" id="drop-label">Klik atau seret file ke sini</p>
                    <p class="text-xs mt-1" style="color: #6B4C4C;">JPG, PNG, PDF — maks. 10MB</p>
                    <input type="file" id="file" name="file" accept=".jpg,.jpeg,.png,.pdf"
                           class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                           onchange="handleFileSelect(this)">
                </div>
                <div id="file-preview" class="hidden mt-3 flex items-center gap-3 p-3 rounded-lg" style="background: #FDF8F8; border: 1px solid #E8D5D5;">
                    <i class="fa-solid fa-file text-lg flex-shrink-0" style="color: #7B1D1D;" id="file-icon"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate" style="color: #1A0A0A;" id="file-name"></p>
                        <p class="text-xs" style="color: #6B4C4C;" id="file-size"></p>
                    </div>
                    <button type="button" onclick="clearFile()" class="text-xs px-2 py-1 rounded-lg transition-all" style="color: #C53030; border: 1px solid #FEB2B2;"
                            onmouseover="this.style.background='#FFF5F5'" onmouseout="this.style.background='transparent'">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('radiology.index') }}"
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

@push('scripts')
<script>
function handleFileSelect(input) {
    if (input.files && input.files[0]) {
        showFilePreview(input.files[0]);
    }
}

function handleDrop(event) {
    event.preventDefault();
    var zone = document.getElementById('drop-zone');
    zone.style.borderColor = '#E8D5D5';
    zone.style.background = 'transparent';
    var file = event.dataTransfer.files[0];
    if (file) {
        var dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('file').files = dt.files;
        showFilePreview(file);
    }
}

function showFilePreview(file) {
    var ext = file.name.split('.').pop().toLowerCase();
    var icon = document.getElementById('file-icon');
    icon.className = 'fa-solid text-lg flex-shrink-0';
    icon.className += ext === 'pdf' ? ' fa-file-pdf' : ' fa-file-image';
    icon.style.color = '#7B1D1D';

    document.getElementById('file-name').textContent = file.name;
    document.getElementById('file-size').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
    document.getElementById('file-preview').classList.remove('hidden');
    document.getElementById('drop-label').textContent = 'File dipilih';
    document.getElementById('drop-icon').style.color = '#7B1D1D';
}

function clearFile() {
    document.getElementById('file').value = '';
    document.getElementById('file-preview').classList.add('hidden');
    document.getElementById('drop-label').textContent = 'Klik atau seret file ke sini';
    document.getElementById('drop-icon').style.color = '#E8D5D5';
}
</script>
@endpush
@endsection
