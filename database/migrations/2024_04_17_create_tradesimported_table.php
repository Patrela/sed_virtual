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
        Schema::create('trades_imported', function (Blueprint $table) {
            $table->id();
            $table->string('name',125);
            $table->string('nit',25)->nullable();
            $table->string('trade_id',40)->unique();
            $table->tinyInteger('is_new')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades_imported');
    }
};
