<?php

namespace App\Utils;

class LinksTranslator
{
    public static array $names = [
        'phone' => 'رقم الهاتف',
        'email' => 'البريد الإلكتروني',
        'whatsapp' => 'واتساب',
        'telegram' => 'تيليجرام',
        'instagram' => 'إنستجرام',
        'snapchat' => 'سناب شات',
        'facebook' => 'فايسبوك',
        'twitter' => 'تويتر'
    ];

    public static function toArabic(string $key): string
    {
        return arrayList()::hasKey($key, self::$names) ? self::$names[$key] : $key;
    }
}
