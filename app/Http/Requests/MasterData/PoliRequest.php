<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PoliRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'kode_poli' => ['required', 'string', 'max:20', Rule::unique('polis', 'kode_poli')->ignore($id)],
            'nama_poli' => ['required', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ];
    }
}
