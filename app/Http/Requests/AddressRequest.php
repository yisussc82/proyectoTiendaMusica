<?php

namespace App\Http\Requests;

use App\Models\ClientAddress;
use App\Rules\PostalCodeMatchesProvince;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        $address = $this->route('address');

        if ($address) {
            return $this->user()->can('update', $address);
        }

        return $this->user()->can('create', ClientAddress::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_default' => $this->boolean('is_default')]);
    }

    public function rules(): array
    {
        return [
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', Rule::in(array_keys(config('provinces')))],
            'postal_code' => ['required', 'digits:5', new PostalCodeMatchesProvince($this->input('province'))],
            'is_default' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'address_line_1' => 'dirección',
            'address_line_2' => 'dirección (línea 2)',
            'city' => 'ciudad',
            'province' => 'provincia',
            'postal_code' => 'código postal',
            'is_default' => 'dirección predeterminada',
        ];
    }
}
