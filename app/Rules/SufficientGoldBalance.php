<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class SufficientGoldBalance implements ValidationRule
{
    public function __construct(public float $amount) {}
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = Auth::user();

        if ($user && $user->gold_balance < $this->amount) {
            $fail('Your gold balance is insufficient to complete this sale.');
        }
    }
}
