<?php

namespace App\Models;

use App\Traits\CreateForCurrentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expenses extends Model
{
    use HasFactory,
        CreateForCurrentUser,
        SoftDeletes;

    protected $guarded = [];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }
}
