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
        Schema::create('products_imported', function (Blueprint $table) {
            $table->id();
            $table->integer('id_provider');
            $table->string('sku', 30);
            $table->string('part_num', 30)->unique();
            $table->string('department', 32)->nullable();
            $table->string('category', 32)->nullable();
            $table->string('segment', 32)->nullable();
            $table->string('brand', 32)->nullable();
            $table->text('attributes')->nullable();
            $table->string('name', 180);
            $table->string('slug', 180)->nullable();
            $table->string('description', 500)->nullable();
            $table->string('short_description', 255)->nullable();
            $table->integer('stock_quantity')->nullable();
            $table->string('unit', 32)->nullable();
            $table->string('guarantee', 180)->nullable();
            $table->decimal('regular_price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->string('price_tax_status', 30)->nullable();
            $table->string('currency', 16)->nullable();
            $table->decimal('dimension_length', 8, 2)->nullable();
            $table->decimal('dimension_width', 8, 2)->nullable();
            $table->decimal('dimension_height', 8, 2)->nullable();
            $table->decimal('dimension_weight', 8, 2)->nullable();
            $table->string('image_1', 255)->nullable();
            $table->string('image_2', 255)->nullable();
            $table->string('image_3', 255)->nullable();
            $table->string('image_4', 255)->nullable();
            $table->string('contact_unit',30)->nullable();
            $table->string('contact_agent',70)->nullable();
            $table->string('contact_email',125)->nullable();
            $table->boolean('is_new')->defaultFalse()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_imported');
    }
};
