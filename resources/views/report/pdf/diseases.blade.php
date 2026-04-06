<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penyakit ICD-10</title>
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
    <h1>Laporan Penyakit Berdasarkan ICD-10</h1>
    <div class="subtitle">
        @if(!empty($filters['date_from']) || !empty($filters['date_to']))
            Periode: {{ $filters['date_from'] ?? '-' }} s/d {{ $filters['date_to'] ?? '-' }}
        @endif
        &nbsp;| Total kode ICD-10: {{ $diseases->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode ICD-10</th>
                <th>Deskripsi Penyakit</th>
                <th>Jumlah Kasus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($diseases as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->icd10_code }}</td>
                <td>{{ $row->icd10Code?->deskripsi ?? '-' }}</td>
                <td>{{ $row->total_kasus }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;color:#999;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
