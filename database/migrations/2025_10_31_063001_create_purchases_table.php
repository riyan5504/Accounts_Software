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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->unsignedBigInteger('vendor_id');
            $table->date('date');
            $table->string('invoice_no')->unique();
            $table->string('reference_no')->nullable();
            $table->string('purchase_type')->nullable();
            $table->string('account_cat')->nullable();
            $table->unsignedBigInteger('debit_account_id')->nullable();
            $table->unsignedBigInteger('payment_account_id')->nullable();
            $table->enum('payment_method', ['cash', 'bank', 'cheque', 'due', 'mobile_bank'])->nullable();
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('unpaid');
            $table->decimal('sub_total', 10, 2);
            $table->decimal('vat_amt', 10, 2)->nullable();
            $table->decimal('dis_percent', 10, 2)->nullable();
            $table->decimal('dis_amt', 10, 2)->nullable();
            $table->decimal('grand_total', 10, 2);
            $table->decimal('paid_amt', 10, 2)->default(0)->nullable();
            $table->decimal('due_amt', 10, 2)->default(0)->nullable();
            $table->string('pay_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
