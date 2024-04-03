<?php

namespace App\Models;

use App\Traits\CreateForCurrentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory, CreateForCurrentUser;

    protected $casts = ['id' => 'string'];
    protected $keyType = 'string';
    protected $guarded = [];

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class, 'account_id')->with(['currency']);
    }
}
