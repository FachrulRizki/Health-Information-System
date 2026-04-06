@extends('layouts.app')

@section('title', 'Hak Akses — ' . ucfirst(str_replace('_', ' ', $role)))

@section('breadcrumb')
    <a href="{{ route('master.dashboard') }}" class="hover:opacity-70 transition-opacity" style="color:#6B4C4C;">Master Data</a>
    <span style="color:#E8D5D5;">/</span>
    <a href="{{ route('master.permissions.index') }}" class="hover:opacity-70 transition-opacity" style="color:#6B4C4C;">Hak Akses</a>
    <span style="color:#E8D5D5;">/</span>
    <span class="font-medium" style="color:#1A0A0A;">{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
@endsection

@section('content')
<div class="fade-in max-w-2xl">

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('master.permissions.index') }}"
       class="w-9 h-9 rounded-xl border flex items-center justify-center transition-colors"
       style="border-color:#E8D5D5; color:#6B4C4C;"
       onmouseover="this.style.background='#FFF0F0'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-arrow-left text-sm"></i>
    </a>
    <div>
        <h2 class="text-xl font-bold" style="color:#1A0A0A;">Hak Akses: {{ ucfirst(str_replace('_', ' ', $role)) }}</h2>
        <p class="text-sm mt-0.5" style="color:#6B4C4C;">
            Berlaku untuk {{ $users->count() }} pengguna dengan peran ini
        </p>
    </div>
</div>

@if($users->isEmpty())
<div class="flex items-start gap-3 rounded-xl border p-4 mb-5" style="background:#FFFBEB; border-color:#FDE68A;">
    <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-0.5" style="color:#B7791F;"></i>
    <p class="text-sm" style="color:#92400E;">Belum ada pengguna dengan peran ini. Pengaturan akan diterapkan saat pengguna ditambahkan.</p>
</div>
@endif

<form method="POST" action="{{ route('master.permissions.update', $role) }}">
    @csrf

    <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #E8D5D5; box-shadow:0 2px 8px rgba(123,29,29,0.06);">
        <div class="px-5 py-3.5 border-b flex items-center justify-between" style="background:#FDF8F8; border-color:#E8D5D5;">
            <span class="text-sm font-semibold flex items-center gap-2" style="color:#1A0A0A;">
                <i class="fa-solid fa-shield-halved" style="color:#7B1D1D;"></i>
                Pilih Menu yang Dapat Diakses
            </span>
            <div class="flex gap-2">
                <button type="button" onclick="toggleAll(true)"
                        class="text-xs px-3 py-1 rounded-lg font-medium"
                        style="background:#F0FFF4; color:#276749;">
                    Pilih Semua
                </button>
                <button type="button" onclick="toggleAll(false)"
                        class="text-xs px-3 py-1 rounded-lg font-medium"
                        style="background:#FFF0F0; color:#7B1D1D;">
                    Hapus Semua
                </button>
            </div>
        </div>

        <div class="p-5 space-y-2">
            @forelse($permissions as $permission)
            <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-colors"
                   style="border:1px solid #F0E8E8;"
                   onmouseover="this.style.background='#FFF8F8'" onmouseout="this.style.background='transparent'">
                <input type="checkbox"
                       name="permissions[]"
                       value="{{ $permission->menu_key }}"
                       class="perm-checkbox w-4 h-4 rounded"
                       style="accent-color:#7B1D1D;"
                       {{ in_array($permission->menu_key, $grantedKeys) ? 'checked' : '' }}>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold" style="color:#1A0A0A; margin:0;">{{ $permission->menu_label }}</p>
                    <p class="text-xs font-mono" style="color:#9B7B7B; margin:0;">{{ $permission->menu_key }}</p>
                </div>
                @if($permission->parent_key)
                <span class="text-xs px-2 py-0.5 rounded-full" style="background:#F9F5F5; color:#9B7B7B;">
                    {{ $permission->parent_key }}
                </span>
                @endif
            </label>
            @empty
            <p class="text-sm text-center py-6" style="color:#9B7B7B;">Belum ada data permission</p>
            @endforelse
        </div>
    </div>

    <div class="flex gap-3 mt-5">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                style="background:linear-gradient(135deg,#7B1D1D,#5C1414); box-shadow:0 2px 8px rgba(123,29,29,0.3);">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Hak Akses
        </button>
        <a href="{{ route('master.permissions.index') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium border transition-colors"
           style="color:#6B4C4C; border-color:#E8D5D5;"
           onmouseover="this.style.background='#FFF0F0'" onmouseout="this.style.background='transparent'">
            <i class="fa-solid fa-xmark"></i> Batal
        </a>
    </div>
</form>

</div>

@push('scripts')
<script>
function toggleAll(state) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = state);
}
</script>
@endpush
@endsection
