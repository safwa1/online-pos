<?php

namespace App\Filament\Resources\PurchaseInvoiceResource\Pages;

use App\Filament\Resources\PurchaseInvoiceResource;
use App\Models\Order;
use App\Models\ProductUnit;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePurchaseInvoice extends CreateRecord
{
    protected static string $resource = PurchaseInvoiceResource::class;

    protected function insertOrder(array $order, mixed $invoiceId): Order
    {
        return Order::query()->create([
            'invoice_id' => $invoiceId,
            'product_id' => $order['product'],
            'quantity'=> $order['quantity'],
            'unit' => ProductUnit::query()->firstWhere('id', 'unit')->toArray()['unit']['name'],
            'unit_price' => $order['unit_price'],
            'total' => $order['total']
        ]);
    }


    protected function handleRecordCreation(array $data): Model
    {
//        "number" => null
//  "supplier_id" => null
//  "invoice_type" => "نقداً"

        // create invoice


        $products = $data['products'];
        foreach ($products as $product) {
            $order = $product['data'];
            // create order of invoice
            // create item
        }

        dd($products);

        return parent::handleRecordCreation($data);
    }

    public function getValidationMessages(): array
    {
        return [
            'price_of_invoice.required' => 'يجب ألا يكون عمود السعر فارغًا.',
        ];
    }

}
