<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class ActionMasterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama'        => ['required', 'string', 'max:200'],
            'icd9cm_code' => ['nullable', 'string', 'max:20'],
        ];
    }
}
