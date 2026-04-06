<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .subtitle { font-size: 11px; color: #555; margin-bottom: 12px; }
        .summary { display: table; width: 100%; margin-bottom: 16px; }
        .summary-item { display: table-cell; border: 1px solid #ccc; padding: 8px 12px; width: 33%; }
        .summary-label { font-size: 10px; color: #666; }
        .summary-value { font-size: 14px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f0f0f0; border: 1px solid #ccc; padding: 5px 8px; text-align: left; }
        td { border: 1px solid #ddd; padding: 4px 8px; }
        tr:nth-child(even) td { background: #fafafa; }
        .footer { margin-top: 16px; font-size: 10px; color: #888; }
    </style>
</head>
<body>
    <h1>Laporan Keuangan</h1>
    <div class="subtitle">
        @if(!empty($filters['date_from']) || !empty($filters['date_to']))
            Periode: {{ $filters['date_from'] ?? '-' }} s/d {{ $filters['date_to'] ?? '-' }}
        @endif
    </div>

    @if($report)
    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Pendapatan Tunai (Umum + Asuransi)</div>
            <div class="summary-value">Rp {{ number_format($report['total_tunai'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Klaim BPJS</div>
            <div class="summary-value">Rp {{ number_format($report['total_bpjs'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Pendapatan</div>
            <div class="summary-value">Rp {{ number_format($report['grand_total'], 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Rawat</th>
                <th>Tanggal</th>
                <th>Nama Pasien</th>
                <th>Metode Pembayaran</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report['bills'] as $bill)
            <tr>
                <td>{{ $bill->visit?->no_rawat ?? '-' }}</td>
                <td>{{ $bill->visit?->tanggal_kunjungan?->format('d/m/Y') ?? '-' }}</td>
                <td>{{ $bill->visit?->patient?->nama_lengkap ?? '-' }}</td>
                <td>{{ strtoupper($bill->payment_method) }}</td>
                <td>Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#999;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <div class="footer">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
