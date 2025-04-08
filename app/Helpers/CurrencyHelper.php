<?php

use App\Models\Currency;

if (!function_exists('format_currency')) {
    function format_currency($amount, Currency $currency = null)
    {
        if (!$currency) {
            $currency = Currency::where('is_default', true)->first();
        }

        if (!$currency) {
            return number_format($amount, 2);
        }

        $formatted_amount = number_format(
            $amount,
            $currency->digits_after_decimal,
            '.',
            ','
        );

        if ($currency->sign_placement === 'before') {
            return $currency->sign  .  $formatted_amount; 
        } else {
            return $formatted_amount  .  $currency->sign;
        }
    }
}