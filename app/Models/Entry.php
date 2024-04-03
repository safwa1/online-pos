<?php

namespace App\Models;

use App\Traits\CreateForCurrentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model
{
    use HasFactory, CreateForCurrentUser,SoftDeletes;

    protected $guarded = [];


    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function getAccount($id, $type): string
    {
        return match ($type) {
            'customer' => Customer::find($id)->name,
            'supplier' => Supplier::find($id)->name,
            'delegate' => Delegate::find($id)->name,
        };
    }

}
