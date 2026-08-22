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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('item_code')->nullable();
            $table->string('item_name');
            $table->unsignedBigInteger('cat_id');
            $table->string('size')->nullable();
            $table->decimal('unit_price');
            $table->decimal('last_purchase_price')->nullable();
            $table->decimal('avg_purchase_price')->nullable();
            $table->decimal('production_cost')->nullable();
            $table->decimal('sales_price')->nullable();
            $table->double('opening_stock')->nullable();
            $table->string('stock_unit')->nullable();
            $table->softDeletes()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
