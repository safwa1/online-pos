<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'product_id',
        'count',
        'barCode',
        'purchasePrice',
        'retailSalePrice',
        'minRetailSalePrice',
        'wholesaleSalePrice',
        'minWholesaleSalePrice',
        'isMainUnit',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
