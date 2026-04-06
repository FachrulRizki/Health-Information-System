<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian — {{ $poli->nama_poli }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes pulse-bg { 0%,100%{background-color:#1d4ed8} 50%{background-color:#2563eb} }
        .calling { animation: pulse-bg 1.5s ease-in-out infinite; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col">
    <header class="bg-gray-800 px-8 py-4 flex justify-between items-center border-b border-gray-700">
        <div>
            <p class="text-gray-400 text-sm uppercase tracking-widest">Antrian</p>
            <h1 class="text-2xl font-bold">{{ $poli->nama_poli }}</h1>
        </div>
        <div class="text-right">
            <p class="text-gray-400 text-sm" id="clock">--:--:--</p>
        </div>
    </header>

    <main class="flex-1 flex flex-col items-center justify-center px-8 py-10 gap-10">
        <div class="w-full max-w-2xl">
            <p class="text-center text-gray-400 text-sm uppercase tracking-widest mb-3">Sedang Dipanggil</p>
            <div id="current-card" class="rounded-2xl p-10 text-center {{ $current ? 'calling' : 'bg-gray-800' }} transition-all">
                <p class="text-8xl font-black tracking-tight" id="current-number">{{ $current?->queue_number ?? '—' }}</p>
                <p class="text-3xl font-semibold mt-4 text-blue-100" id="current-name">
                    {{ $current?->visit?->patient?->nama_lengkap ?? 'Belum ada pasien dipanggil' }}
                </p>
            </div>
        </div>

        <div class="w-full max-w-2xl">
            <p class="text-center text-gray-400 text-sm uppercase tracking-widest mb-3">Antrian Berikutnya</p>
            <div id="waiting-list" class="grid grid-cols-5 gap-3">
                @forelse($waiting as $entry)
                    <div class="bg-gray-800 rounded-xl p-4 text-center border border-gray-700">
                        <p class="text-3xl font-bold text-yellow-400">{{ $entry->queue_number }}</p>
                        <p class="text-xs text-gray-400 mt-1 truncate">{{ $entry->visit?->patient?->nama_lengkap ?? '-' }}</p>
                    </div>
                @empty
                    <div class="col-span-5 text-center text-gray-600 py-4">Tidak ada antrian menunggu.</div>
                @endforelse
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 px-8 py-3 text-center text-gray-500 text-xs border-t border-gray-700">
        {{ config('app.name') }} — Display Antrian Real-time
    </footer>

<script>
const POLI_ID = {{ $poliId }};
setInterval(() => { document.getElementById('clock').textContent = new Date().toLocaleTimeString('id-ID'); }, 1000);

function applyQueueUpdate(data) {
    if (data.status === 'dipanggil') {
        document.getElementById('current-card').className = 'rounded-2xl p-10 text-center calling transition-all';
        document.getElementById('current-number').textContent = data.queue_number;
        document.getElementById('current-name').textContent   = data.patient_name ?? '-';
    }
}

if (typeof window.Echo !== 'undefined') {
    window.Echo.channel(`poli.${POLI_ID}`).listen('QueueStatusUpdated', applyQueueUpdate);
} else {
    setInterval(() => location.reload(), 10000);
}
</script>
</body>
</html>
