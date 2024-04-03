<?php


use App\Utils\ArrayList;

if (!function_exists('valueOf')) {
    function valueOf($case): ?string
    {
        if (!enum_exists(get_class($case))) return null;
        return $case->value;
    }
}

if (!function_exists('no_space')) {
    function no_space(string $input): string
    {
        return preg_replace("/\s+/", "", $input);
    }
}

if (!function_exists('to_currency')) {
    function to_currency(string|int|float $input, ?string $prefix = null): string
    {
        $fmt = numfmt_create('en_US', NumberFormatter::CURRENCY);
        $res = numfmt_format_currency($fmt, floatval("{$input}"), "USA");
        $final = str_replace("USA", "", $res);
        if (str_contains($final, ".00")) {
            $final = str_replace(".00", "", $final);
        }
        return $prefix != null ? "{$final} {$prefix}" : $final;
    }
}

if (!function_exists('to_html_currency')) {

    function to_html_currency(
        string|int|float $input,
        ?string $prefix = null,
        string $prefixTag = 'span'
    ): string
    {
        $result = to_currency($input);
        return $prefix != null
            ? "{$result} <{$prefixTag}>{$prefix}</{$prefixTag}>"
            : $result;
    }
}

if (!function_exists('arrayList')) {
    function arrayList(): ArrayList {
        return new ArrayList();
    }
}
