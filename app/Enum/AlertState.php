<?php

namespace App\Enum;

enum AlertState : string
{
    case on = "on";
    case off = "off";

    public static function get(string $name): ?AlertState
    {
        $name = strtoupper(trim($name));
        if(empty($name))
            return null;

        foreach(AlertState::cases() as $status)
        {
            if($status->name == $name)
                return $status;
        }
        return null;
    }
}
