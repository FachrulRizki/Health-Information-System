<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MasterDataService
{
    private array $references = [
        \App\Models\Poli::class => [
            ['table' => 'sub_polis', 'fk' => 'poli_id'],
            ['table' => 'doctor_schedules', 'fk' => 'poli_id'],
        ],
        \App\Models\Doctor::class => [
            ['table' => 'doctor_schedules', 'fk' => 'doctor_id'],
        ],
        \App\Models\Drug::class => [
            ['table' => 'prescription_items', 'fk' => 'drug_id'],
            ['table' => 'drug_stocks', 'fk' => 'drug_id'],
        ],
        \App\Models\DrugCategory::class => [
            ['table' => 'drugs', 'fk' => 'drug_category_id'],
        ],
        \App\Models\DrugUnit::class => [
            ['table' => 'drugs', 'fk' => 'drug_unit_id'],
        ],
        \App\Models\Icd10Code::class => [
            ['table' => 'diagnoses', 'fk' => 'icd10_code'],
        ],
        \App\Models\Icd9cmCode::class => [
            ['table' => 'procedures', 'fk' => 'icd9cm_code'],
        ],
        \App\Models\Room::class => [
            ['table' => 'beds', 'fk' => 'room_id'],
        ],
        \App\Models\Specialization::class => [
            ['table' => 'doctors', 'fk' => 'specialization_id'],
            ['table' => 'sub_specializations', 'fk' => 'specialization_id'],
        ],
    ];

    private array $searchFields = [
        \App\Models\Poli::class           => ['kode_poli', 'nama_poli'],
        \App\Models\Doctor::class         => ['kode_dokter', 'nama_dokter'],
        \App\Models\Drug::class           => ['kode', 'nama'],
        \App\Models\Icd10Code::class      => ['kode', 'deskripsi'],
        \App\Models\Icd9cmCode::class     => ['kode', 'deskripsi'],
        \App\Models\Room::class           => ['kode_kamar', 'nama_kamar'],
        \App\Models\DrugCategory::class   => ['nama'],
        \App\Models\DrugUnit::class       => ['nama'],
        \App\Models\Specialization::class => ['nama'],
        \App\Models\Supplier::class       => ['nama'],
    ];

    public function getAll(string $model, array $filters = []): LengthAwarePaginator
    {
        $query = $model::query();

        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $fields = $this->searchFields[$model] ?? ['nama'];
            $query->where(function ($sub) use ($fields, $q) {
                foreach ($fields as $field) {
                    $sub->orWhere($field, 'like', "%{$q}%");
                }
            });
        }

        return $query->latest()->paginate(15)->withQueryString();
    }

    public function create(string $model, array $data): Model
    {
        return $model::create($data);
    }

    public function update(string $model, int $id, array $data): Model
    {
        $record = $model::findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    public function delete(string $model, int $id): void
    {
        $record = $model::findOrFail($id);

        if (isset($this->references[$model])) {
            foreach ($this->references[$model] as $ref) {
                $count = DB::table($ref['table'])->where($ref['fk'], $id)->count();
                if ($count > 0) {
                    throw new RuntimeException(
                        "Data tidak dapat dihapus karena masih direferensikan oleh {$count} data di tabel {$ref['table']}."
                    );
                }
            }
        }

        $record->delete();
    }
}
