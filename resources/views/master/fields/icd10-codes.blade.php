<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Kode ICD-10</label>
    <input type="text" name="kode" value="{{ old('kode', $record?->kode) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
    <textarea name="deskripsi" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" rows="3" required>{{ old('deskripsi', $record?->deskripsi) }}</textarea>
</div>
