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
        Schema::create('users_visit_log', function (Blueprint $table) {
            $table->id();
            $table->string('user_id',40);
            $table->string('trade_id',40);
            $table->smallInteger('year_log');
            $table->tinyInteger('month_log');
            $table->tinyInteger('day_log');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_visit_log');
    }
};
