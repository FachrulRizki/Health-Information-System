<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting API — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Setting API</h1>
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">← Dashboard</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid gap-4">
        @forelse($settings as $setting)
            <div class="bg-white rounded shadow p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="font-semibold text-gray-800">{{ strtoupper(str_replace('_', ' ', $setting->integration_name)) }}</span>
                        @if($setting->isTestingMode())
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">⚠ Testing</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">✓ Production</span>
                        @endif
                        @if($setting->is_active)
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">Aktif</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 truncate">Endpoint: {{ $setting->endpoint_url }}</p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <button type="button" onclick="testConnection('{{ $setting->integration_name }}')"
                        class="text-sm px-3 py-1.5 rounded border border-gray-300 text-gray-600 hover:bg-gray-50">Test Koneksi</button>
                    <form method="POST" action="{{ route('master.api-settings.toggle', $setting->integration_name) }}">
                        @csrf
                        <button type="submit" class="text-sm px-3 py-1.5 rounded border {{ $setting->is_active ? 'border-orange-300 text-orange-600' : 'border-green-300 text-green-600' }}">
                            {{ $setting->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <a href="{{ route('master.api-settings.edit', $setting->integration_name) }}"
                        class="text-sm px-3 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-700">Edit</a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded shadow p-8 text-center text-gray-400">Belum ada konfigurasi API.</div>
        @endforelse
    </div>
</div>

<div id="testModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg p-6 max-w-md w-full mx-4">
        <h2 class="font-semibold text-gray-800 mb-3">Hasil Uji Koneksi</h2>
        <div id="testResult" class="text-sm text-gray-600 mb-4"></div>
        <button onclick="document.getElementById('testModal').classList.add('hidden')"
            class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm">Tutup</button>
    </div>
</div>

<script>
function testConnection(name) {
    document.getElementById('testModal').classList.remove('hidden');
    document.getElementById('testResult').innerHTML = '<span class="text-gray-400">Menguji...</span>';
    fetch(`/master/api-settings/${name}/test-connection`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
    }).then(r => r.json()).then(d => {
        document.getElementById('testResult').innerHTML = d.success
            ? `<span class="text-green-600 font-medium">✓ Berhasil</span><br>${d.message ?? ''}`
            : `<span class="text-red-600 font-medium">✗ Gagal</span><br>${d.message ?? ''}`;
    }).catch(() => {
        document.getElementById('testResult').innerHTML = '<span class="text-red-600">Gagal menghubungi server.</span>';
    });
}
</script>
</body>
</html>
