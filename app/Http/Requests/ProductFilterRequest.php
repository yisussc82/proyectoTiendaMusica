<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'on_sale' => $this->boolean('on_sale'),
            'in_stock' => $this->boolean('in_stock'),
        ]);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'exists:categories,slug'],
            'on_sale' => ['boolean'],
            'in_stock' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'search' => 'búsqueda',
            'category' => 'categoría',
            'on_sale' => 'en oferta',
            'in_stock' => 'en stock',
        ];
    }
}
