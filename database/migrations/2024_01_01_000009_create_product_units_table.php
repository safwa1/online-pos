<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->unsigned()->constrained('products')->cascadeOnDelete();
            $table->float('count');
            $table->string('barCode', 50)->nullable();
            $table->decimal('purchasePrice', 10, 2);
            $table->decimal('retailSalePrice', 10, 2)->nullable();
            $table->decimal('minRetailSalePrice', 10, 2)->nullable();
            $table->decimal('wholesaleSalePrice', 10, 2)->nullable();
            $table->decimal('minWholesaleSalePrice', 10, 2)->nullable();
            $table->boolean('isMainUnit')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
