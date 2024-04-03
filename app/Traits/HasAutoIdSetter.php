<?php


namespace App\Traits;

use Illuminate\Support\Str;

/**
 * @method static addGlobalScope(\Closure $param)
 * @method static creating(\Closure $param)
 */
trait HasAutoIdSetter {

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function($model) {
            $id = Str::uuid();
            $model->id = $id;
        });

    }
}
