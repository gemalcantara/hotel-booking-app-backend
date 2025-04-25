<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PromoCodeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip validation if the value is null or empty
        if (empty($value)) {
            return;
        }
        
        // Check if the promo code is uppercase and between 6-10 characters
        if (!preg_match('/^[A-Z0-9]{6,10}$/', $value)) {
            $fail('The :attribute must be uppercase alphanumeric and between 6-10 characters.');
        }
    }
}
