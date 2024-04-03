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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->unsigned()->nullable()->constrained()->cascadeOnDelete();
            $table->string('barCode', 50)->nullable();
            $table->string('name', 255);
            $table->string('description', 255)->nullable();
            $table->decimal('purchasePrice', 10, 2)->nullable();
            $table->decimal('retailSalePrice', 10, 2)->nullable();
            $table->decimal('minRetailSalePrice', 10, 2)->nullable();
            $table->decimal('wholesaleSalePrice', 10, 2)->nullable();
            $table->decimal('minWholesaleSalePrice', 10, 2)->nullable();
            $table->integer('minQuantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
