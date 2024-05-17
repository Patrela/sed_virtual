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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->integer('id_category')->autoIncrement();
            $table->string('part_num', 20)->index();
            $table->integer('id');
            $table->string('name', 32);
            $table->string('slug', 32);
            $table->string('group_name', 16)->nullable();
            $table->integer('parent_id')->nullable();
            //$table->unsignedBigInteger('part_num')->nullable();
            $table->foreign('part_num')->references('part_num')->on('products')->onDelete('cascade');
            $table->unique(['part_num', 'parent_id', 'id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
