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
        Schema::create('section_total_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_id');
            $table->decimal('raw_grand_price', 10, 2);
            $table->decimal('pack_grand_price', 10, 2);
            $table->decimal('labor_grand_price', 10, 2)->nullable();
            $table->decimal('depreciation_grand_price', 10, 2)->nullable();
            $table->decimal('utility_grand_price', 10, 2)->nullable();
            $table->decimal('overhead_grand_price', 10, 2)->nullable();
            $table->decimal('transport_grand_price', 10, 2)->nullable();
            $table->decimal('qc_grand_price', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_total_costs');
    }
};
