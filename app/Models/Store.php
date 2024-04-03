<?php

namespace App\Models;

use App\Traits\CreateForCurrentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory, CreateForCurrentUser;

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'manager_name',
        'manager_phone',
        'manager_email',
    ];

}
