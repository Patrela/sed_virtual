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
        Schema::create('users_imported', function (Blueprint $table) {
            $table->id();
            $table->string('trade_name',125)->default('SED International de Colombia S.A.S');
            $table->string('trade_nit',25)->default('8300361083');
            $table->string('trade_id',40)->nullable()->default('1');
            $table->string('name',125);
            $table->string('email',125)->unique();
            $table->tinyInteger('role_type')->nullable();
            $table->tinyInteger('is_new')->default(0)->index();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_imported');
    }
};
