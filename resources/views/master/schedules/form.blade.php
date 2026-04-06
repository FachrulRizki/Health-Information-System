<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $schedule ? 'Edit' : 'Tambah' }} Jadwal Praktik — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $schedule ? 'Edit' : 'Tambah' }} Jadwal Praktik Dokter</h1>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $schedule ? route('master.schedules.update', $schedule->id) : route('master.schedules.store') }}"
          class="bg-white rounded shadow p-6 space-y-4">
        @csrf
        @if($schedule) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dokter <span class="text-red-500">*</span></label>
            <select name="doctor_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                <option value="">-- Pilih Dokter --</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ old('doctor_id', $schedule?->doctor_id) == $doctor->id ? 'selected' : '' }}>
                        {{ $doctor->nama_dokter }}
                        @if($doctor->specialization) — {{ $doctor->specialization->nama }} @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Poli <span class="text-red-500">*</span></label>
            <select name="poli_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                <option value="">-- Pilih Poli --</option>
                @foreach($polis as $poli)
                    <option value="{{ $poli->id }}" {{ old('poli_id', $schedule?->poli_id) == $poli->id ? 'selected' : '' }}>
                        {{ $poli->nama_poli }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Hari <span class="text-red-500">*</span></label>
            <select name="hari" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                <option value="">-- Pilih Hari --</option>
                @foreach(['senin','selasa','rabu','kamis','jumat','sabtu','minggu'] as $hari)
                    <option value="{{ $hari }}" {{ old('hari', $schedule?->hari) === $hari ? 'selected' : '' }}>
                        {{ ucfirst($hari) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                <input type="time" name="jam_mulai"
                       value="{{ old('jam_mulai', $schedule ? substr($schedule->jam_mulai, 0, 5) : '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                <input type="time" name="jam_selesai"
                       value="{{ old('jam_selesai', $schedule ? substr($schedule->jam_selesai, 0, 5) : '') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Pasien per Sesi <span class="text-red-500">*</span></label>
            <input type="number" name="kuota" min="1" max="999"
                   value="{{ old('kuota', $schedule?->kuota ?? 20) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
        </div>

        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $schedule?->is_active ?? true) ? 'checked' : '' }}>
            <label for="is_active" class="text-sm text-gray-700">Jadwal Aktif</label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 text-sm">Simpan</button>
            <a href="{{ route('master.schedules.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 text-sm">Batal</a>
        </div>
    </form>
</div>
</body>
</html>
