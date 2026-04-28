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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('name');
            $table->string('batch_no');
            $table->string('batch_size');
            $table->date('date');
            $table->string('ra_name');
            $table->double('raw_qty', 10, 2);
            $table->string('raw_unit', 10)->nullable();
            $table->decimal('raw_u_price', 10, 2);
            $table->decimal('raw_t_price', 10, 2);
            $table->double('pulp', 10, 2)->nullable();
            $table->string('pulp_unit', 10)->nullable();
            $table->double('yield', 10, 2)->nullable();
            $table->string('yield_unit', 10)->nullable();
            $table->double('ex_qty', 10, 2)->nullable();
            $table->string('ex_unit', 10)->nullable();
            $table->decimal('yield_percent', 8, 2)->nullable();
            $table->double('final_qty', 10, 2);
            $table->string('final_unit', 10)->nullable();
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('grand_total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
