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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_number')->index();
            $table->tinyInteger('item');
            $table->string('part_num', 30)->index();
            $table->string('trade_part_num', 40);
            $table->string('product_name', 180);
            $table->integer('quantity')->nullable();
            $table->decimal('sed_unit_price', 14, 4)->nullable();
            $table->decimal('sed_total_price', 18, 4)->nullable();
            $table->decimal('sed_tax_value', 14, 4)->nullable();
            $table->boolean('is_tax_applied')->default(0)->nullable();
            $table->string('currency', 5)->default("COP");
            $table->timestamps();
            $table->foreign('order_number')->references('order_number')->on('orders')->onDelete('cascade');
            $table->foreign('part_num')->references('part_num')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
