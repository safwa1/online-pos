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
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('account_id')->nullable();
            $table->string('account_type')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('statement');
            $table->decimal('creditor', 10, 2)->default(0);
            $table->decimal('debtor', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('document', 100)->nullable();
            $table->string('document_number', 100)->nullable();
            $table->dateTime('date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
