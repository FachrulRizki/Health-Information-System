<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 4px; }
        h2 { font-size: 13px; border-bottom: 1px solid #ccc; padding-bottom: 4px; margin-top: 16px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 16px; margin-bottom: 12px; }
        .info-label { color: #666; font-size: 10px; }
        .info-value { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f5f5f5; text-align: left; padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
        .total-row td { font-weight: bold; background: #f5f5f5; }
        .footer { margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .sign-box { border-top: 1px solid #333; padding-top: 4px; text-align: center; margin-top: 60px; }
    </style>
</head>
<body>
    <h1>DOKUMEN KLAIM BPJS</h1>
    <p style="text-align:center; font-size:10px; color:#666;">{{ config('app.name') }} — Dicetak: {{ now()->format('d/m/Y H:i') }}</p>

    <h2>Data Kunjungan</h2>
    <div class="info-grid">
        <div><div class="info-label">No. Rawat</div><div class="info-value">{{ $visit->no_rawat }}</div></div>
        <div><div class="info-label">No. SEP</div><div class="info-value">{{ $visit->no_sep ?? '-' }}</div></div>
        <div><div class="info-label">Tanggal Kunjungan</div><div class="info-value">{{ $visit->tanggal_kunjungan?->format('d/m/Y') }}</div></div>
        <div><div class="info-label">Poli</div><div class="info-value">{{ $visit->poli?->nama_poli }}</div></div>
        <div><div class="info-label">Dokter</div><div class="info-value">{{ $visit->doctor?->nama_dokter ?? '-' }}</div></div>
        <div><div class="info-label">Jenis Penjamin</div><div class="info-value">{{ strtoupper($visit->jenis_penjamin) }}</div></div>
    </div>

    <h2>Data Pasien</h2>
    <div class="info-grid">
        <div><div class="info-label">Nama Lengkap</div><div class="info-value">{{ $visit->patient?->nama_lengkap }}</div></div>
        <div><div class="info-label">No. RM</div><div class="info-value">{{ $visit->patient?->no_rm }}</div></div>
        <div><div class="info-label">No. BPJS</div><div class="info-value">{{ $visit->patient?->no_bpjs ?? '-' }}</div></div>
        <div><div class="info-label">Tanggal Lahir</div><div class="info-value">{{ $visit->patient?->tanggal_lahir?->format('d/m/Y') }}</div></div>
    </div>

    <h2>Diagnosa ICD-10</h2>
    <table>
        <thead><tr><th>Kode</th><th>Deskripsi</th><th>Tipe</th></tr></thead>
        <tbody>
            @foreach($visit->diagnoses as $d)
                <tr>
                    <td>{{ $d->icd10_code }}</td>
                    <td>{{ $d->icd10Code?->deskripsi }}</td>
                    <td>{{ $d->is_primary ? 'Utama' : 'Sekunder' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($visit->procedures->count())
    <h2>Tindakan ICD-9 CM</h2>
    <table>
        <thead><tr><th>Kode</th><th>Deskripsi</th></tr></thead>
        <tbody>
            @foreach($visit->procedures as $p)
                <tr><td>{{ $p->icd9cm_code }}</td><td>{{ $p->icd9cmCode?->deskripsi }}</td></tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($visit->bill)
    <h2>Rincian Tagihan</h2>
    <table>
        <thead><tr><th>Item</th><th>Tipe</th><th style="text-align:right">Harga</th><th style="text-align:right">Qty</th><th style="text-align:right">Subtotal</th></tr></thead>
        <tbody>
            @foreach($visit->bill->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->item_type }}</td>
                    <td style="text-align:right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td style="text-align:right">{{ $item->quantity }}</td>
                    <td style="text-align:right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align:right">Total</td>
                <td style="text-align:right">Rp {{ number_format($visit->bill->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <div class="footer">
        <div><div class="sign-box">Petugas Klaim</div></div>
        <div><div class="sign-box">Kepala Keuangan</div></div>
    </div>
</body>
</html>
