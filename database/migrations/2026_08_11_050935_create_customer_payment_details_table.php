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
        Schema::create('customer_payment_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->foreignId('customer_payment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sales_id')
                ->constrained('sales')
                ->cascadeOnDelete();

            $table->decimal('paid_amount', 15, 2);

            $table->timestamps();

            $table->index(['company_id', 'sales_id']);
            $table->index(['company_id', 'customer_payment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_payment_details');
    }
};
