<?php


namespace App\Utils;

use Hashids\Hashids;


class Hashed
{
    protected static string $HASH_KEY = "UggxnUtBHrl1MlJIN/XSEhrsLA21y9I8yAEQ+k1vwaY=";
    // singleton instance
    protected static ?Hashids $instance = null;

    // get singleton instance
    public static function new(): ?Hashids
    {
        if (self::$instance === null) {
            self::$instance =  new Hashids(self::$HASH_KEY, 9);
        }
        return self::$instance;
    }

}

