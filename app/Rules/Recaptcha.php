<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $endpoint = config('recaptcha.url');

        $response = Http::asForm()->post($endpoint, [
            'secret' => config('recaptcha.secret_key'),
            'response' => $value,
        ])->json();

        if (! $response['success'] && ! $response['score'] > 0.5) {
            $fail('The captcha is incorrect.');
        }
    }
}
