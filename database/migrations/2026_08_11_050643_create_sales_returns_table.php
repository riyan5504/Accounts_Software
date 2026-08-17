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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->restrictOnDelete();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('invoice_no');
            $table->string('reference')->nullable();
            $table->text('narration')->nullable();
            $table->string('payment_status')->nullable();
            $table->foreignId('debit_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('credit_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->decimal('sub_total', 15, 2);
            $table->decimal('vat_amt', 15, 2)->default(0);
            $table->decimal('dis_percent', 8, 3)->default(0);
            $table->decimal('dis_amt', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['company_id', 'vendor_id']);
            $table->index(['company_id', 'purchase_id']);
            $table->index(['company_id', 'date']);
            $table->unique(['company_id', 'invoice_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};
