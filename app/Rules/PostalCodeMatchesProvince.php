<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PostalCodeMatchesProvince implements ValidationRule
{
    public function __construct(private ?string $province)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $prefix = config('provinces')[$this->province] ?? null;

        if ($prefix === null) {
            return;
        }

        if (! str_starts_with((string) $value, $prefix)) {
            $fail('El :attribute no corresponde a la provincia elegida.');
        }
    }
}
