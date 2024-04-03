<?php

namespace App\Models;

use App\Traits\CreateForCurrentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountsGroup extends Model
{
    use HasFactory, CreateForCurrentUser;

    protected $guarded = [];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'group_id');
    }
}
