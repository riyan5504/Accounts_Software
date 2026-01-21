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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cat_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('purchase_id');
            $table->integer('qty');            
            $table->decimal('unit_price');
            $table->decimal('price');
            $table->decimal('vat_percent')->nullable();
            $table->decimal('vat_amount')->nullable();
            $table->decimal('total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
