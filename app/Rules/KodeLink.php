<?php

namespace App\Rules;

use App\Models\Field;
use App\Models\Paraf;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class KodeLink implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Harus angka
        if (!ctype_digit($value)) {
            $fail('Kode harus berupa angka.');
            return;
        }

        // Harus 5 digit
        if (strlen($value) !== 5) {
            $fail('Kode harus terdiri dari 5 digit.');
            return;
        }

        $field = Field::where('kode', $value)->exists();
        $paraf = Paraf::where('kode', $value)->exists();

        // Minimal harus ada di salah satu tabel
        if (! $field && ! $paraf) {
            $fail('Kode tidak valid.');
        }
    }
}
