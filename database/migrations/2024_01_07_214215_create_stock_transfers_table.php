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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};


/*

CREATE TABLE StockTransfers (
    TransferID INT PRIMARY KEY,
    ProductID INT,
    QuantityTransferred INT,
    TransferDate DATE,
    FOREIGN KEY (ProductID) REFERENCES Products(ProductID)
);

INSERT INTO StockTransfers (ProductID, QuantityTransferred, TransferDate)
VALUES (1, 100, '2024-01-08');

 */
