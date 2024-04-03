<?php

namespace App\Utils;

use Illuminate\Support\Arr;

class ArrayList extends Arr
{
    public static function size(array $array) : int
    {
        return $array == null ? 0 : count($array);
    }

    public static function isEmpty(array $array) : bool
    {
        return empty($array);
    }

    public static function keys(array $array, mixed $filter_value, bool $strict = false): array
    {
        return array_keys($array, $filter_value, $strict);
    }

    public static function values(array $array): array
    {
        return array_values($array);
    }

    public static function addOrSet(array &$array, $key, $value): void
    {
        $array[$key] = $value;
    }

    public static function valueOf(array $array, $key): mixed
    {
        return $array[$key];
    }

    public static function hasKey($key, array $array): bool {
        return array_key_exists($key ,$array);
    }

    public static function reIndex(array $array): array {
        return array_values($array);
    }
}
