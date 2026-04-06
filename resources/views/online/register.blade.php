<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Online — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-3xl mx-auto p-6">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-blue-700">Pendaftaran Online</h1>
        <p class="text-gray-500 mt-1 text-sm">{{ config('app.name') }}</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('online.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b">Data Pasien</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir *</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                        max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon') }}" maxlength="20"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b">Jenis Penjamin</h2>
            <div class="flex gap-6 mb-4">
                @foreach(['umum' => 'Umum', 'bpjs' => 'BPJS', 'asuransi' => 'Asuransi'] as $val => $label)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="jenis_penjamin" value="{{ $val }}"
                            {{ old('jenis_penjamin', 'umum') === $val ? 'checked' : '' }}
                            onchange="togglePenjamin(this.value)">
                        <span class="text-sm font-medium">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <div id="bpjs-fields" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">No. BPJS *</label>
                <input type="text" name="no_bpjs" value="{{ old('no_bpjs') }}" maxlength="20"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>
            <div id="asuransi-fields" class="hidden grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Polis *</label>
                    <input type="text" name="no_polis_asuransi" value="{{ old('no_polis_asuransi') }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Asuransi *</label>
                    <input type="text" name="nama_asuransi" value="{{ old('nama_asuransi') }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b">Pilih Jadwal Kunjungan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poli *</label>
                    <select name="poli_id" id="poli_id" required onchange="loadSchedules()"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">-- Pilih Poli --</option>
                        @foreach($polis as $poli)
                            <option value="{{ $poli->id }}" {{ old('poli_id') == $poli->id ? 'selected' : '' }}>{{ $poli->nama_poli }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kunjungan *</label>
                    <input type="date" name="tanggal_kunjungan" id="tanggal_kunjungan"
                        value="{{ old('tanggal_kunjungan', date('Y-m-d')) }}"
                        min="{{ date('Y-m-d') }}" required onchange="loadSchedules()"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
            </div>

            <div id="schedule-loading" class="mt-4 hidden text-sm text-gray-500">Memuat jadwal...</div>
            <div id="schedule-container" class="mt-4 hidden">
                <p class="text-sm font-medium text-gray-700 mb-2">Pilih Dokter / Jadwal *</p>
                <div id="schedule-list" class="space-y-2"></div>
                <input type="hidden" name="doctor_id" id="doctor_id" value="{{ old('doctor_id') }}">
            </div>
            <div id="schedule-empty" class="mt-4 hidden text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded px-3 py-2">Tidak ada jadwal tersedia.</div>
            <div id="alternatives-container" class="mt-4 hidden">
                <p class="text-sm font-semibold text-gray-600 mb-2">Jadwal Alternatif:</p>
                <div id="alternatives-list" class="space-y-1 text-sm text-gray-700"></div>
            </div>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-8 py-2.5 rounded-lg hover:bg-blue-700 text-sm font-medium">Daftar Sekarang</button>
    </form>
</div>

<script>
function togglePenjamin(v) {
    document.getElementById('bpjs-fields').classList.toggle('hidden', v !== 'bpjs');
    document.getElementById('asuransi-fields').classList.toggle('hidden', v !== 'asuransi');
}

function loadSchedules() {
    const poliId  = document.getElementById('poli_id').value;
    const tanggal = document.getElementById('tanggal_kunjungan').value;
    ['schedule-container','schedule-empty','alternatives-container'].forEach(id => document.getElementById(id).classList.add('hidden'));
    if (!poliId || !tanggal) return;
    document.getElementById('schedule-loading').classList.remove('hidden');
    fetch(`{{ route('online.schedules') }}?poli_id=${poliId}&tanggal=${tanggal}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('schedule-loading').classList.add('hidden');
            renderSchedules(data.schedules, data.alternatives);
        })
        .catch(() => document.getElementById('schedule-loading').classList.add('hidden'));
}

function renderSchedules(schedules, alternatives) {
    const list = document.getElementById('schedule-list');
    list.innerHTML = '';
    if (!schedules || schedules.length === 0) {
        document.getElementById('schedule-empty').classList.remove('hidden');
        renderAlternatives(alternatives);
        return;
    }
    schedules.forEach(s => {
        const div = document.createElement('div');
        div.className = `border rounded-lg p-3 cursor-pointer transition ${s.is_full ? 'opacity-50 cursor-not-allowed bg-gray-50' : 'hover:border-blue-400 hover:bg-blue-50'}`;
        div.innerHTML = `<div class="flex justify-between items-center"><div><p class="font-medium text-sm">${s.doctor_name}</p><p class="text-xs text-gray-500">${s.jam_mulai} – ${s.jam_selesai}</p></div><div>${s.is_full ? '<span class="text-xs text-red-600 font-semibold">Penuh</span>' : `<span class="text-xs text-green-600 font-semibold">Sisa ${s.available} slot</span>`}</div></div>`;
        if (!s.is_full) div.addEventListener('click', () => {
            document.querySelectorAll('#schedule-list > div').forEach(d => d.classList.remove('border-blue-500','ring-2','ring-blue-300'));
            div.classList.add('border-blue-500','ring-2','ring-blue-300');
            document.getElementById('doctor_id').value = s.doctor_id;
        });
        list.appendChild(div);
    });
    document.getElementById('schedule-container').classList.remove('hidden');
    renderAlternatives(alternatives);
}

function renderAlternatives(alternatives) {
    if (!alternatives || alternatives.length === 0) return;
    const list = document.getElementById('alternatives-list');
    list.innerHTML = '';
    alternatives.forEach(a => {
        const p = document.createElement('p');
        p.textContent = `${a.hari}, ${a.tanggal} — ${a.doctor_name} (${a.jam_mulai}–${a.jam_selesai}) · Sisa ${a.available} slot`;
        list.appendChild(p);
    });
    document.getElementById('alternatives-container').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('input[name="jenis_penjamin"]:checked');
    if (checked) togglePenjamin(checked.value);
    const poliId = document.getElementById('poli_id').value;
    const tanggal = document.getElementById('tanggal_kunjungan').value;
    if (poliId && tanggal) loadSchedules();
});
</script>
</body>
</html>
