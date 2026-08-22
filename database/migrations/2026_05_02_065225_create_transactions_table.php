<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('module_type', 30); // purchase, sale, return
            $table->unsignedBigInteger('module_id');
            $table->string('reference_no', 100)->nullable();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->enum('payment_method', ['cash', 'bank', 'cheque', 'mobile_bank', 'due'])->nullable();
            $table->decimal('paid_amt', 15, 2)->default(0);
            $table->decimal('receive_amt', 15, 2)->default(0);
            $table->decimal('return_amt', 15, 2)->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['company_id', 'date']);
            $table->index(['module_type', 'module_id']);
            $table->index(['company_id', 'vendor_id']);
            $table->index(['company_id', 'payment_method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};