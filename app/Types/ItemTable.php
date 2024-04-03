<?php

namespace App\Types;

class ItemTable
{
    public function __construct(
        public readonly ?int $product_id,
        public readonly ?string $store_id,
        public readonly ?string $invoice_id,
        public readonly ?int  $quantity,
        public readonly ?float  $purchasePrice,
        public readonly ?float  $retailSalePrice,
        public readonly ?float  $minRetailSalePrice,
        public readonly ?float  $wholesaleSalePrice,
        public readonly ?float  $minWholesaleSalePrice,
        public readonly ?string $expireDate
    )
    {
    }
}
