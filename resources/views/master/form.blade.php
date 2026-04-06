@extends('layouts.app')

@section('title', ($record ? 'Edit' : 'Tambah') . ' ' . $label)

@section('breadcrumb')
    <span style="color: #6B4C4C;">Master Data</span>
    <i class="fa-solid fa-chevron-right text-xs mx-2" style="color: #E8D5D5;"></i>
    <a href="{{ route("{$routePrefix}.index") }}" class="hover:opacity-70" style="color: #6B4C4C;">{{ $label }}</a>
    <i class="fa-solid fa-chevron-right text-xs mx-2" style="color: #E8D5D5;"></i>
    <span class="font-medium" style="color: #1A0A0A;">{{ $record ? 'Edit' : 'Tambah' }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route("{$routePrefix}.index") }}"
           class="w-9 h-9 rounded-xl border flex items-center justify-center hover:bg-red-50 transition-colors"
           style="color: #7B1D1D; border-color: #E8D5D5;">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold" style="color: #1A0A0A;">{{ $record ? 'Edit' : 'Tambah' }} {{ $label }}</h2>
            <p class="text-sm mt-0.5" style="color: #6B4C4C;">{{ $record ? 'Perbarui data' : 'Tambahkan data baru' }} {{ strtolower($label) }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 rounded-xl border p-4 mb-5" style="background: #FEF2F2; border-color: #FECACA;">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5" style="color: #EF4444;"></i>
            <div>
                <p class="text-sm font-semibold mb-1" style="color: #B91C1C;">Terdapat kesalahan:</p>
                <ul class="text-sm space-y-0.5" style="color: #DC2626;">
                    @foreach($errors->all() as $e)<li class="flex items-center gap-1"><i class="fa-solid fa-minus text-xs"></i> {{ $e }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl overflow-hidden" style="border: 1px solid #E8D5D5; box-shadow: 0 1px 3px rgba(123,29,29,0.06);">
        <div class="px-5 py-3.5 border-b flex items-center gap-2" style="background: #F9F5F5; border-color: #E8D5D5;">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: #FFF5F5;">
                <i class="fa-solid fa-sliders text-xs" style="color: #7B1D1D;"></i>
            </div>
            <h3 class="text-sm font-semibold" style="color: #1A0A0A;">Data {{ $label }}</h3>
        </div>
        <form method="POST"
              action="{{ $record ? route("{$routePrefix}.update", $record->id) : route("{$routePrefix}.store") }}"
              class="p-5 space-y-4">
            @csrf
            @if($record) @method('PUT') @endif

            @include("master.fields.{$entity}", ['record' => $record])

            <div class="flex gap-3 pt-2 border-t" style="border-color: #E8D5D5;">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                        style="background: linear-gradient(135deg, #7B1D1D, #5C1414); box-shadow: 0 2px 8px rgba(123,29,29,0.3);">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan
                </button>
                <a href="{{ route("{$routePrefix}.index") }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium border hover:bg-red-50 transition-colors"
                   style="color: #6B4C4C; border-color: #E8D5D5;">
                    <i class="fa-solid fa-xmark"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
