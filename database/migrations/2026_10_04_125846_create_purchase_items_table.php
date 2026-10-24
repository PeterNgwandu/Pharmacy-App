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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_id')
                  ->constrained('purchases')
                  ->cascadeOnDelete();

            $table->foreignId('medicine_id')
                  ->constrained('medicines')
                  ->restrictOnDelete();

            $table->string('batch_number');

            $table->date('manufactured_at')->nullable();

            $table->date('expires_at');

            $table->integer('quantity');

            $table->decimal('purchase_price', 12, 2);

            $table->decimal('selling_price', 12, 2);

            $table->decimal('subtotal', 12, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
