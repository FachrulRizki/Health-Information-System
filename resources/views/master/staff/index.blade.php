<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Petugas Non-Dokter — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Petugas Non-Dokter</h1>
        <a href="{{ route('master.staff.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">+ Tambah Petugas</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-sm">{{ session('error') }}</div>
    @endif

    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari username..." class="border border-gray-300 rounded px-3 py-2 text-sm flex-1">
        <select name="role" class="border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="">Semua Peran</option>
            @foreach($staffRoles as $role)
                <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded text-sm">Filter</button>
        <a href="{{ route('master.staff.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm">Reset</a>
    </form>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600">#</th>
                    <th class="px-4 py-3 text-left text-gray-600">Username</th>
                    <th class="px-4 py-3 text-left text-gray-600">Peran</th>
                    <th class="px-4 py-3 text-left text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($staff as $member)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $member->id }}</td>
                        <td class="px-4 py-3 font-medium">{{ $member->username }}</td>
                        <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $member->role) }}</td>
                        <td class="px-4 py-3">
                            @if($member->is_active)
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs">Aktif</span>
                            @else
                                <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('master.staff.edit', $member->id) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('master.staff.destroy', $member->id) }}" onsubmit="return confirm('Nonaktifkan petugas ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Nonaktifkan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Tidak ada data petugas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $staff->links() }}</div>
</div>
</body>
</html>
