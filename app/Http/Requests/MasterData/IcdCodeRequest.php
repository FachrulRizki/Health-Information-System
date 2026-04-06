<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IcdCodeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id    = $this->route('id');
        $table = str_contains($this->path(), 'icd9cm') ? 'icd9cm_codes' : 'icd10_codes';
        return [
            'kode'      => ['required', 'string', 'max:20', Rule::unique($table, 'kode')->ignore($id)],
            'deskripsi' => ['required', 'string', 'max:500'],
        ];
    }
}
