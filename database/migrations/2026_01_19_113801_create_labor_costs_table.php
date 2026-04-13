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
        Schema::create('labor_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_id');
            $table->string('labor_name')->nullable();
            $table->double('duty_day', 10, 2)->nullable();
            $table->decimal('d_pay', 10, 2)->nullable();
            $table->decimal('total_pay', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labor_costs');
    }
};
