<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function createNewItem($item): void
    {
        Item::create([
            'product_id' => $item['product_id'],
            'store_id' => $item['store_id'],
            'invoice_id' => $item['invoice_id'],
            'quantity' => $item['quantity'],
            'purchasePrice' => $item['purchasePrice'],
            'retailSalePrice' => $item['retailSalePrice'],
            'minRetailSalePrice' => $item['minRetailSalePrice'],
            'wholesaleSalePrice' => $item['wholesaleSalePrice'],
            'minWholesaleSalePrice' => $item['minRetailSalePrice'],
            'expireDate' => $item['expireDate'],
        ]);
    }

}
