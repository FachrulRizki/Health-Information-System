<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit API — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-2xl mx-auto p-6">
    <a href="{{ route('master.api-settings.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2 mb-6">Edit: {{ strtoupper(str_replace('_', ' ', $setting->integration_name)) }}</h1>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('master.api-settings.update', $setting->integration_name) }}" class="bg-white rounded shadow p-6 space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">URL Endpoint Produksi *</label>
            <input type="url" name="endpoint_url" value="{{ old('endpoint_url', $setting->endpoint_url) }}" required
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">URL Sandbox</label>
            <input type="url" name="sandbox_url" value="{{ old('sandbox_url', $setting->sandbox_url) }}"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Consumer Key</label>
            <input type="password" name="consumer_key" autocomplete="new-password"
                placeholder="{{ $setting->consumer_key_encrypted ? '(tersimpan — kosongkan jika tidak ingin mengubah)' : 'Masukkan consumer key' }}"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            <p class="text-xs text-gray-400 mt-1">Akan dienkripsi sebelum disimpan.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Consumer Secret</label>
            <input type="password" name="consumer_secret" autocomplete="new-password"
                placeholder="{{ $setting->consumer_secret_encrypted ? '(tersimpan — kosongkan jika tidak ingin mengubah)' : 'Masukkan consumer secret' }}"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            <p class="text-xs text-gray-400 mt-1">Akan dienkripsi sebelum disimpan.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Mode Operasi</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="mode" value="testing" {{ old('mode', $setting->mode) === 'testing' ? 'checked' : '' }}>
                    <span class="text-sm"><span class="px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">⚠ Testing</span></span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="mode" value="production" {{ old('mode', $setting->mode) === 'production' ? 'checked' : '' }}>
                    <span class="text-sm"><span class="px-2 py-0.5 rounded text-xs bg-green-100 text-green-800">✓ Production</span></span>
                </label>
            </div>
        </div>
        <div>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $setting->is_active) ? 'checked' : '' }} class="w-4 h-4">
                <span class="text-sm font-medium text-gray-700">Integrasi Aktif</span>
            </label>
        </div>
        <div class="flex gap-3 pt-2 border-t border-gray-100">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 text-sm">Simpan</button>
            <a href="{{ route('master.api-settings.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded text-sm">Batal</a>
        </div>
    </form>
</div>
</body>
</html>
