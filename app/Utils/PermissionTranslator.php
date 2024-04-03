<?php

namespace App\Utils;

class PermissionTranslator
{
    public static function toArabic(string $permission): string
    {
        return match ($permission) {
            'create' => 'كتابة',
            'read' => 'قراءة',
            'update' => 'تعديل',
            'delete' => 'حذف',
            default => $permission
        };
    }
}
