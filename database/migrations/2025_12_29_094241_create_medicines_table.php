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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('brand_name')->nullable();

            $table->foreignID('category_id')->constrained();
            $table->foreignID('dosage_form_id')->constrained();

            $table->string('strength');
            $table->string('barcode')->nullable()->unique();

            $table->string('base_unit'); // tablet, ml
            $table->string('pack_unit'); // strip, bottle
            $table->integer('units_per_pack');

            $table->integer('reorder_level')->default(0);
            $table->boolean('status')->default(true);

            $table->foreignID('created_by')->constrained('users');
            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
