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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->date('date');
            $table->string('invoice_no', 50);
            $table->string('reference', 100)->nullable();
            $table->text('narration')->nullable();
            $table->foreignId('payment_account_id ')->nullable()->constrained('accounts')->nullOnDelete();
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('unpaid');
            $table->decimal('sub_total', 15, 2);
            $table->decimal('vat_amt', 15, 2)->default(0);
            $table->decimal('dis_percent', 8, 3)->default(0);
            $table->decimal('dis_amt', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->decimal('due_amt', 15, 2)->default(0);
            $table->string('pay_receive', 100)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'date']);
            $table->index(['company_id', 'vendor_id']);
            $table->index(['company_id', 'payment_status']);
            $table->unique(['company_id', 'invoice_no'], 'unique_invoice_per_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
