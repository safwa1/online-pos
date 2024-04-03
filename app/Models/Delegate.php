<?php

namespace App\Models;

use App\Traits\CreateForCurrentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delegate extends Model
{
    use HasFactory, CreateForCurrentUser;

    protected $guarded = [];
    protected $casts = ['id' => 'string'];
    protected $keyType = 'string';

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class, 'account_id')->with(['currency']);
    }
}
