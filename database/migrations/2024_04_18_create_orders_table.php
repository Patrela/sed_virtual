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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('order')->unique();
            $table->string('trade_id',40);
            $table->timestamp('created')->useCurrent();
            $table->string('user_id',40)->nullable();
            $table->string('contact_email',125)->nullable();
            $table->timestamps();
            $table->foreign('trade_id')->references('trade_id')->on('trades')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
