<?php

namespace App\Models;

use App\Traits\CreateForCurrentUser;
use App\Traits\HasAutoIdSetter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, CreateForCurrentUser;

    protected $casts = ['id' => 'string'];
    protected $keyType = 'string';
    protected $guarded = [];

    public function delegate(): BelongsTo
    {
        return $this->belongsTo(Delegate::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(AccountsGroup::class, 'group_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class, 'account_id')->with(['currency']);
    }
}
