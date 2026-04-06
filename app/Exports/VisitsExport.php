<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VisitsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $visits) {}

    public function collection(): Collection
    {
        return $this->visits;
    }

    public function headings(): array
    {
        return ['No. Rawat', 'Tanggal', 'Nama Pasien', 'No. RM', 'Poli', 'Dokter', 'Jenis Penjamin', 'Status'];
    }

    public function map($visit): array
    {
        return [
            $visit->no_rawat,
            $visit->tanggal_kunjungan?->format('d/m/Y'),
            $visit->patient?->nama_lengkap ?? '-',
            $visit->patient?->no_rm ?? '-',
            $visit->poli?->nama_poli ?? '-',
            $visit->doctor?->nama_dokter ?? '-',
            strtoupper($visit->jenis_penjamin),
            $visit->status,
        ];
    }
}
