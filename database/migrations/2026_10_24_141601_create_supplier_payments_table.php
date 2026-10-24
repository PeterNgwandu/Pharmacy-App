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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_id')->constrained('purchases')->restrictOnDelete();

            // Amount paid to supplier
            $table->decimal('amount', 12, 2);

            // Payment Method
            $table->string('payment_method', 30);

            // Optional transaction or receipt reference
            $table->string('payment_reference')->nullable();

            // Date the payment was made
            $table->date('payment_date');

            // Additional payment information
            $table->text('notes')->nullable();

            // User who recorded the payment
            $table->foreignId('created_by')->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Helps when receiving payments for a purchase date
            $table->index(['purchase_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
