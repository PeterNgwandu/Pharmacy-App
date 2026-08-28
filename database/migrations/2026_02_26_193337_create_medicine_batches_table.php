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
        Schema::create('medicine_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medicine_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('batch_number');

            $table->date('manufactured_at')->nullable();
            $table->date('expires_at');

            // Pricing Per Batch
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('selling_price', 10, 2);

            // This will be updated on through stock movement
            $table->integer('quantity')->default(0);
            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique(['medicine_id', 'batch_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_batches');
    }
};
