<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilterByCurrentUser
{
    protected static function boot(): void
    {
        parent::boot();

        self::creating(function($model) {
            $model->user_id = auth()->id();
        });

        self::addGlobalScope(function (Builder $builder) {
            $builder->where('user_id', auth()->id());
        });
    }
}
