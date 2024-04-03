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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3);
            $table->string('name', 255);
            $table->string('country', 255)->nullable();
            $table->string('symbol', 10)->nullable();
            $table->integer('decimal_places');
            $table->decimal('exchange_rate', 10, 4);
            $table->decimal('smallest_unit_rate', 10, 4)->nullable();
            $table->string('smallest_unit_name', 50)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
