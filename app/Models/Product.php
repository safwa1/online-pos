<?php

namespace App\Models;

use App\Traits\CreateForCurrentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, CreateForCurrentUser;


    protected $guarded = [];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productUnit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class)->with(['unit']);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class)->with(['unit']);
    }
}
