<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kunjungan Pasien</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .subtitle { font-size: 11px; color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f0f0f0; border: 1px solid #ccc; padding: 5px 8px; text-align: left; }
        td { border: 1px solid #ddd; padding: 4px 8px; }
        tr:nth-child(even) td { background: #fafafa; }
        .footer { margin-top: 16px; font-size: 10px; color: #888; }
    </style>
</head>
<body>
    <h1>Laporan Kunjungan Pasien</h1>
    <div class="subtitle">
        @if(!empty($filters['date_from']) || !empty($filters['date_to']))
            Periode: {{ $filters['date_from'] ?? '-' }} s/d {{ $filters['date_to'] ?? '-' }}
        @endif
        &nbsp;| Total: {{ $visits->count() }} kunjungan
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Rawat</th>
                <th>Tanggal</th>
                <th>Nama Pasien</th>
                <th>No. RM</th>
                <th>Poli</th>
                <th>Dokter</th>
                <th>Penjamin</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($visits as $visit)
            <tr>
                <td>{{ $visit->no_rawat }}</td>
                <td>{{ $visit->tanggal_kunjungan?->format('d/m/Y') }}</td>
                <td>{{ $visit->patient?->nama_lengkap ?? '-' }}</td>
                <td>{{ $visit->patient?->no_rm ?? '-' }}</td>
                <td>{{ $visit->poli?->nama_poli ?? '-' }}</td>
                <td>{{ $visit->doctor?->nama_dokter ?? '-' }}</td>
                <td>{{ strtoupper($visit->jenis_penjamin) }}</td>
                <td>{{ $visit->status }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:#999;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
