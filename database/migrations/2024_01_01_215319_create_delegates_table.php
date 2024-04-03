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
        Schema::create('delegates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->string('name', 255)->unique()->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->integer('limit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delegates');
    }
};
