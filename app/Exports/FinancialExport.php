<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FinancialExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $report) {}

    public function collection(): Collection
    {
        return $this->report['bills'];
    }

    public function headings(): array
    {
        return ['No. Rawat', 'Tanggal', 'Nama Pasien', 'Metode Pembayaran', 'Total'];
    }

    public function map($bill): array
    {
        return [
            $bill->visit?->no_rawat ?? '-',
            $bill->visit?->tanggal_kunjungan?->format('d/m/Y') ?? '-',
            $bill->visit?->patient?->nama_lengkap ?? '-',
            strtoupper($bill->payment_method),
            $bill->total_amount,
        ];
    }
}
