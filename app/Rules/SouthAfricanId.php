<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SouthAfricanId implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check the South African ID number that its correct length
        if (! preg_match('/^\d{13}$/', $value)) {
            $fail('The :attribute must be a valid 13-digit South African ID number.');

            return;
        }

        // Luhn checksum validation
        $sum = 0;
        $alt = false;

        for ($i = strlen($value) - 1; $i >= 0; $i--) {
            $n = (int) $value[$i];
            if ($alt) {
                $n *= 2;
                if ($n > 9) {
                    $n -= 9;
                }
            }
            $sum += $n;
            $alt = ! $alt;
        }

        if ($sum % 10 !== 0) {
            $fail('The :attribute is not a valid South African ID number.');
        }
    }
}
