<?php

namespace App\Utils;

class MediableArrayHelper
{
    public static function next(array $array, &$currentIndex = 0): array
    {
        $result =  $array[$currentIndex];
        $length = count($array);
        if(($currentIndex + 1) < $length) {
            $currentIndex++;
            $result = $array[$currentIndex];
        }

        return $result;
    }

    public static function previous(array $array, &$currentIndex = 0): array
    {
        $result =  $array[$currentIndex];
         if(($currentIndex - 1) >= 0) {
             $currentIndex--;
             $result = $array[$currentIndex];
         }
         return $result;
    }
}
