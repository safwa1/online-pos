<?php

namespace App\Utils;

class Translator
{
    public static array $names = [
        'on' => 'يعمل',
        'off' => 'متوقف',
        'info' => 'معلومات',
        'error' => 'خطأ',
        'warning' => 'تحذير',
        'success' => 'نجاح',
    ];

    public static function toArabic(string $key): string
    {
        return arrayList()::hasKey($key, self::$names) ? self::$names[$key] : $key;
    }
}
