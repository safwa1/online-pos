<?php

namespace App\Enum;

enum AlertType : string
{
    case info = "info";
    case error = "error";
    case warning = "warning";
    case success = "success";

    public static function get(string $name): ?AlertType
    {
        $name = strtoupper(trim($name));
        if(empty($name))
            return null;

        foreach(AlertType::cases() as $status)
        {
            if($status->name == $name)
                return $status;
        }
        return null;
    }
}
