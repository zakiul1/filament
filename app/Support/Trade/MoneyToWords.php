<?php

namespace App\Support\Trade;

use NumberFormatter;

final class MoneyToWords
{
    public static function toEnglish(float $amount, ?string $currencyCode = null): string
    {
        $amount = round($amount, 2);

        $intPart = (int) floor($amount);
        $fraction = (int) round(($amount - $intPart) * 100);

        $fmt = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        $wordsInt = trim($fmt->format($intPart));

        $currencyCode = strtoupper($currencyCode ?? '');
        $currencyWord = $currencyCode ? " {$currencyCode}" : '';

        // Example output: "one thousand two hundred USD and 25/100 only"
        if ($fraction > 0) {
            return "{$wordsInt}{$currencyWord} and {$fraction}/100 only";
        }

        return "{$wordsInt}{$currencyWord} only";
    }
}