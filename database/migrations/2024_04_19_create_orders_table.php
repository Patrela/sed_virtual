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
            $table->bigInteger('order_number')->unique();
            $table->string('order_id',40);
            $table->string('trade_nit',25);
            $table->string('buyer_name',125);
            $table->string('buyer_email',125);
            $table->string('trade_request_code',25);
            $table->tinyInteger('request_status')->default(1);
            $table->string('transaction_cus',25);
            $table->timestamp('transaction_date_time')->useCurrent();
            $table->string('receiver_name',125);
            $table->string('receiver_identification',25);
            $table->string('receiver_phone',25);
            $table->string('receiver_address',125);
            $table->integer('receiver_department_id');
            $table->integer('receiver_country_id');
            $table->tinyInteger('delivery_purpose')->default(0);
            $table->string('delivery_type',15)->nullable();
            $table->decimal('delivery_extra_cost', 14, 4)->nullable();
            $table->decimal('delivery_extra_cost_tax', 14, 4)->nullable();
            $table->string('transport_type',15)->nullable();
            $table->string('transport_company',125)->nullable();
            $table->string('notes',255)->nullable();
            $table->string('coupon_id',25)->nullable();
            $table->string('coupon_name',50)->nullable();
            $table->decimal('coupon_value', 14, 4)->nullable();
            $table->timestamp('coupon_date')->nullable();
            $table->string('coupon_currency',5)->nullable();
            $table->timestamps();
            $table->foreign('trade_nit')->references('nit')->on('trades')->onDelete('cascade');
            $table->unique(['trade_nit', 'trade_request_code']);

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
