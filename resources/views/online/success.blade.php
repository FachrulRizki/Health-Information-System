<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
<div class="max-w-lg w-full">
    <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-1">Pendaftaran Berhasil!</h1>
        <p class="text-gray-500 text-sm mb-6">Simpan informasi berikut sebagai bukti pendaftaran Anda.</p>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
            <p class="text-xs text-blue-500 font-semibold uppercase tracking-wide mb-1">Nomor Antrian</p>
            <p class="text-5xl font-extrabold text-blue-700">{{ $visit->queueEntry?->queue_number ?? '-' }}</p>
        </div>

        <div class="text-left space-y-3 text-sm mb-6">
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">No. Rawat</span>
                <span class="font-semibold text-gray-800">{{ $visit->no_rawat }}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Nama Pasien</span>
                <span class="font-semibold text-gray-800">{{ $visit->patient?->nama_lengkap }}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Poli</span>
                <span class="font-semibold text-gray-800">{{ $visit->poli?->nama_poli }}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Tanggal Kunjungan</span>
                <span class="font-semibold text-gray-800">{{ $visit->tanggal_kunjungan?->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Jenis Penjamin</span>
                <span class="font-semibold text-gray-800 capitalize">{{ $visit->jenis_penjamin }}</span>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-left text-sm text-yellow-800 mb-6">
            <p class="font-semibold mb-2">📋 Petunjuk untuk Pasien:</p>
            <ol class="list-decimal list-inside space-y-1">
                <li>Hadir ke fasilitas kesehatan sesuai tanggal kunjungan.</li>
                <li>Tunjukkan nomor antrian <strong>{{ $visit->queueEntry?->queue_number }}</strong> kepada petugas.</li>
                <li>Bawa kartu identitas (KTP/KK) dan kartu penjamin jika ada.</li>
                <li>Datang 15 menit sebelum jadwal praktik dimulai.</li>
            </ol>
        </div>

        <a href="{{ route('online.index') }}" class="inline-block bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 text-sm font-medium">
            Daftar Kunjungan Lain
        </a>
    </div>
    <p class="text-center text-xs text-gray-400 mt-4">{{ config('app.name') }}</p>
</div>
</body>
</html>
