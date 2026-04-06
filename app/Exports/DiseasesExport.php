<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DiseasesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $diseases) {}

    public function collection(): Collection
    {
        return $this->diseases;
    }

    public function headings(): array
    {
        return ['No.', 'Kode ICD-10', 'Deskripsi Penyakit', 'Jumlah Kasus'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;
        return [
            $i,
            $row->icd10_code,
            $row->icd10Code?->deskripsi ?? '-',
            $row->total_kasus,
        ];
    }
}
