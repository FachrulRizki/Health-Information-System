@php
    $categories = \App\Models\DrugCategory::orderBy('nama')->get();
    $units       = \App\Models\DrugUnit::orderBy('nama')->get();
    $suppliers   = \App\Models\Supplier::orderBy('nama')->get();
@endphp

@if($record?->kode)
<div>
    <label class="block text-sm font-medium mb-1" style="color:#6B4C4C;">Kode Obat</label>
    <div class="w-full border rounded px-3 py-2 text-sm font-mono" style="background:#F9F5F5; border-color:#E8D5D5; color:#9B7B7B;">
        {{ $record->kode }}
    </div>
    <p class="text-xs mt-1" style="color:#9B7B7B;">Kode obat digenerate otomatis oleh sistem</p>
</div>
@else
<div>
    <label class="block text-sm font-medium mb-1" style="color:#6B4C4C;">Kode Obat</label>
    <div class="w-full border rounded px-3 py-2 text-sm font-mono" style="background:#F9F5F5; border-color:#E8D5D5; color:#9B7B7B;">
        Akan digenerate otomatis (OBT-XXXXX)
    </div>
</div>
@endif

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Obat <span class="text-red-500">*</span></label>
    <input type="text" name="nama" value="{{ old('nama', $record?->nama) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
    <select name="drug_category_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
        <option value="">-- Pilih Kategori --</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('drug_category_id', $record?->drug_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
    <select name="drug_unit_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
        <option value="">-- Pilih Satuan --</option>
        @foreach($units as $unit)
            <option value="{{ $unit->id }}" {{ old('drug_unit_id', $record?->drug_unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->nama }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
    <select name="supplier_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
        <option value="">-- Pilih Supplier --</option>
        @foreach($suppliers as $sup)
            <option value="{{ $sup->id }}" {{ old('supplier_id', $record?->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->nama }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli <span class="text-red-500">*</span></label>
    <input type="number" name="harga_beli" value="{{ old('harga_beli', $record?->harga_beli ?? 0) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" min="0" step="0.01" required>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual <span class="text-red-500">*</span></label>
    <input type="number" name="harga_jual" value="{{ old('harga_jual', $record?->harga_jual ?? 0) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" min="0" step="0.01" required>
</div>
<div class="flex items-center gap-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" id="is_active_drug"
           {{ old('is_active', $record?->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active_drug" class="text-sm text-gray-700">Aktif</label>
</div>

{{-- Stok --}}
@php $existingStock = $record?->stocks?->first(); @endphp
<div style="border-top:1px solid #E8D5D5; padding-top:1rem; margin-top:1rem;">
    <h4 class="text-sm font-semibold mb-3" style="color:#7B1D1D;">
        <i class="fa-solid fa-boxes-stacked mr-1"></i>
        {{ $record ? 'Update Stok' : 'Stok Awal' }}
    </h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">
                Jumlah Stok <span style="color:#C53030;">*</span>
            </label>
            <input type="number" name="initial_stock"
                   value="{{ old('initial_stock', $existingStock?->quantity ?? '') }}"
                   min="0" step="0.01" required
                   class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">
                Stok Minimum <span style="color:#C53030;">*</span>
            </label>
            <input type="number" name="minimum_stock"
                   value="{{ old('minimum_stock', $existingStock?->minimum_stock ?? 10) }}"
                   min="0" step="0.01" required
                   class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">
                Tanggal Kadaluarsa <span style="color:#C53030;">*</span>
            </label>
            <input type="date" name="expiry_date"
                   value="{{ old('expiry_date', $existingStock?->expiry_date?->format('Y-m-d') ?? '') }}"
                   required
                   class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
        </div>
        <div>
            <label class="block text-xs font-semibold mb-1.5" style="color:#6B4C4C;">No. Batch</label>
            <input type="text" name="batch_number"
                   value="{{ old('batch_number', $existingStock?->batch_number ?? '') }}"
                   placeholder="Nomor batch obat"
                   class="w-full border rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2"
                   style="border-color:#E8D5D5; --tw-ring-color:rgba(123,29,29,0.15);">
        </div>
    </div>
</div>
