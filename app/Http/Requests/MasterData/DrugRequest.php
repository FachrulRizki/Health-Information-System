<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class DrugRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'nama'             => ['required', 'string', 'max:200'],
            'drug_category_id' => ['required', 'integer', 'exists:drug_categories,id'],
            'drug_unit_id'     => ['required', 'integer', 'exists:drug_units,id'],
            'supplier_id'      => ['nullable', 'integer', 'exists:suppliers,id'],
            'harga_beli'       => ['required', 'numeric', 'min:0'],
            'harga_jual'       => ['required', 'numeric', 'min:0'],
            'is_active'        => ['boolean'],
        ];
    }
}
