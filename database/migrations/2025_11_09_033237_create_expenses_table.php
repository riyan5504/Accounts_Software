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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('voucher_no');
            $table->string('reference_no')->nullable();
            $table->unsignedBigInteger('expense_account_id'); // GL expense account
            $table->unsignedBigInteger('payment_account_id'); // cash/bank
            $table->enum('payment_method', ['cash', 'bank', 'cheque', 'due', 'mobile_bank']);
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('unpaid');
            $table->decimal('sub_total', 15, 2);
            $table->decimal('tax_rate', 5, 2)->default(0)->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0)->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0)->nullable();
            $table->decimal('due_amount', 15, 2)->default(0)->nullable();
            $table->string('attachment')->nullable();
            $table->string('expense_type')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('pay_to')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
