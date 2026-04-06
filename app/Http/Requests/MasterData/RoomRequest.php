<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'kode_kamar' => ['required', 'string', 'max:20', Rule::unique('rooms', 'kode_kamar')->ignore($id)],
            'nama_kamar' => ['required', 'string', 'max:100'],
            'kelas'      => ['required', Rule::in(['1', '2', '3', 'VIP'])],
            'kapasitas'  => ['required', 'integer', 'min:1'],
            'is_active'  => ['boolean'],
        ];
    }
}
