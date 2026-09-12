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
        // Fix sale_item_batches relationship
        Schema::table('sale_item_batches', function (Blueprint $table) {

            // Remove the incorrect foreign key
            $table->dropForeign(['sale_id']);

            // Rename sale_id to sale_item_id
            $table->renameColumn('sale_id', 'sale_item_id');
        });

        Schema::table('sale_item_batches', function (Blueprint $table) {

            // Re-create the correct foreign key
            $table->foreign('sale_item_id')
                  ->references('id')
                  ->on('sale_items')
                  ->cascadeOnDelete();
        });

        // Fix payments relationship

        Schema::table('payments', function (Blueprint $table) {

            // Remove old foreign key
            $table->dropForeign(['sales_id']);

            // Rename sales_id to sale_id
            $table->renameColumn('sales_id', 'sale_id');
        });

        Schema::table('payments', function (Blueprint $table) {

            // Re-create correct foreign key
            $table->foreign('sale_id')
                  ->references('id')
                  ->on('sales')
                  ->cascadeOnDelete();

            // Add payment reference
            $table->string('reference')->nullable()->after('amount');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Revert payments
        |--------------------------------------------------------------------------
        */

        Schema::table('payments', function (Blueprint $table) {

            $table->dropForeign(['sale_id']);

            $table->renameColumn('sale_id', 'sales_id');

            $table->enum('payment_method', [
                'CASH',
                'LIPA_HAPA',
                'BANK'
            ])->change();

            $table->dropColumn('reference');
        });

        Schema::table('payments', function (Blueprint $table) {

            $table->foreign('sales_id')
                  ->references('id')
                  ->on('sales')
                  ->cascadeOnDelete();
        });


        /*
        |--------------------------------------------------------------------------
        | Revert sale_item_batches
        |--------------------------------------------------------------------------
        */

        Schema::table('sale_item_batches', function (Blueprint $table) {

            $table->dropForeign(['sale_item_id']);

            $table->renameColumn('sale_item_id', 'sale_id');
        });

        Schema::table('sale_item_batches', function (Blueprint $table) {

            $table->foreign('sale_id')
                  ->references('id')
                  ->on('sale_items')
                  ->cascadeOnDelete();
        });
    }
};
