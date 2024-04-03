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
        Schema::create('stock_issues', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_issues');
    }
};

/*

CREATE TABLE StockIssues (
    IssueID INT PRIMARY KEY,
    ProductID INT,
    QuantityIssued INT,
    IssueDate DATE,
    FOREIGN KEY (ProductID) REFERENCES Products(ProductID)
);


INSERT INTO StockIssues (ProductID, QuantityIssued, IssueDate)
VALUES (1, 50, '2024-01-10');


 */
