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
        Schema::create('affinities', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('brand', 32);
            $table->string('name', 80);
            $table->string('url', 180);
            $table->string('image', 60);
            $table->boolean('is_active')->defaultFalse()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affinities');
    }
};
