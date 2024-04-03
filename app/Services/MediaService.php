<?php

namespace App\Services;

class MediaService
{
    public static function delete($file): void
    {
        unlink(public_path('storage/' . $file));
    }
}
