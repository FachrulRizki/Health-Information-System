<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Kamar</label>
    <input type="text" name="kode_kamar" value="{{ old('kode_kamar', $record?->kode_kamar) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kamar</label>
    <input type="text" name="nama_kamar" value="{{ old('nama_kamar', $record?->nama_kamar) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
    <select name="kelas" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
        @foreach(['1','2','3','VIP'] as $k)
            <option value="{{ $k }}" {{ old('kelas', $record?->kelas) == $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas</label>
    <input type="number" name="kapasitas" value="{{ old('kapasitas', $record?->kapasitas ?? 1) }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" min="1">
</div>
