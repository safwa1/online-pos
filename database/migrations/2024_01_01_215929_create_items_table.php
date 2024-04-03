<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->constrained();
            $table->foreignId("store_id")->nullable()->constrained();
            $table->foreignId("invoice_id")->nullable()->constrained("purchase_invoices");
            $table->decimal("quantity");
            $table->decimal('purchasePrice', 10, 2)->nullable();
            $table->decimal('retailSalePrice', 10, 2)->nullable();
            $table->decimal('minRetailSalePrice', 10, 2)->nullable();
            $table->decimal('wholesaleSalePrice', 10, 2)->nullable();
            $table->decimal('minWholesaleSalePrice', 10, 2)->nullable();
            $table->date("expireDate")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
