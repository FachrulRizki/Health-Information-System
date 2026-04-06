<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Praktik Dokter — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Jadwal Praktik Dokter</h1>
        <a href="{{ route('master.schedules.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">+ Tambah Jadwal</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-sm">{{ session('error') }}</div>
    @endif

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari dokter / poli..." class="border border-gray-300 rounded px-3 py-2 text-sm flex-1 min-w-[180px]">
        <select name="doctor_id" class="border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="">Semua Dokter</option>
            @foreach($doctors as $doctor)
                <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->nama_dokter }}</option>
            @endforeach
        </select>
        <select name="poli_id" class="border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="">Semua Poli</option>
            @foreach($polis as $poli)
                <option value="{{ $poli->id }}" {{ request('poli_id') == $poli->id ? 'selected' : '' }}>{{ $poli->nama_poli }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded text-sm">Filter</button>
        <a href="{{ route('master.schedules.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm">Reset</a>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600">#</th>
                    <th class="px-4 py-3 text-left text-gray-600">Dokter</th>
                    <th class="px-4 py-3 text-left text-gray-600">Poli</th>
                    <th class="px-4 py-3 text-left text-gray-600">Hari</th>
                    <th class="px-4 py-3 text-left text-gray-600">Jam Mulai</th>
                    <th class="px-4 py-3 text-left text-gray-600">Jam Selesai</th>
                    <th class="px-4 py-3 text-left text-gray-600">Kuota</th>
                    <th class="px-4 py-3 text-left text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($schedules as $schedule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $schedule->id }}</td>
                        <td class="px-4 py-3 font-medium">{{ $schedule->doctor->nama_dokter }}</td>
                        <td class="px-4 py-3">{{ $schedule->poli->nama_poli }}</td>
                        <td class="px-4 py-3 capitalize">{{ $schedule->hari }}</td>
                        <td class="px-4 py-3">{{ substr($schedule->jam_mulai, 0, 5) }}</td>
                        <td class="px-4 py-3">{{ substr($schedule->jam_selesai, 0, 5) }}</td>
                        <td class="px-4 py-3">{{ $schedule->kuota }}</td>
                        <td class="px-4 py-3">
                            @if($schedule->is_active)
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs">Aktif</span>
                            @else
                                <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('master.schedules.edit', $schedule->id) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('master.schedules.destroy', $schedule->id) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">Tidak ada jadwal praktik.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $schedules->links() }}</div>
</div>
</body>
</html>
