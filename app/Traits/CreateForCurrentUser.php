<?php


namespace App\Traits;

use App\Models\Customer;
use App\Models\Delegate;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @method static addGlobalScope(\Closure $param)
 * @method static creating(\Closure $param)
 */
trait CreateForCurrentUser {

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function($model) {
            if ($model instanceof Customer || $model instanceof Supplier || $model instanceof Delegate) {
                $id = Str::uuid();
                $model->id = $id;
            }

           $model->user_id = auth()->id();
        });

    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
