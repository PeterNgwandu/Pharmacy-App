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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medicine_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('medicine_batch_id')
                  ->constrained('medicine_batches')
                  ->cascadeOnDelete();

            $table->enum('type', [
                'IN',
                'OUT',
                'ADJUSTMENT',
                'RETURN',
                'EXPIRED'
            ]);

            // Always store positive number
            $table->integer('quantity');

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('reference')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
