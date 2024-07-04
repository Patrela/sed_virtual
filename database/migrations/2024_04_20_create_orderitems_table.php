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
            $table->integer('order')->index();
            $table->tinyInteger('item');
            $table->string('part_num', 20);
            $table->string('name', 180);
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->decimal('total_price', 16, 2)->nullable();
            $table->timestamps();
            $table->foreign('order')->references('order')->on('orders')->onDelete('cascade');
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
