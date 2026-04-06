<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class DoctorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'nama_dokter'           => ['required', 'string', 'max:200'],
            'specialization_id'     => ['nullable', 'integer', 'exists:specializations,id'],
            'sub_specialization_id' => ['nullable', 'integer', 'exists:sub_specializations,id'],
            'no_sip'                => ['nullable', 'string', 'max:50'],
            'no_telepon'            => ['nullable', 'string', 'max:20'],
            'is_active'             => ['nullable', 'boolean'],
        ];
    }
}
