<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    //protected $primaryKey = 'part_num';
    protected $fillable = [
        'order_number',
        'order_id',
        'trade_nit',
        'buyer_name',
        'buyer_email',
        'trade_request_code',
        'request_status',
        'transaction_cus',
        'transaction_date_time',
        'receiver_name',
        'receiver_identification',
        'receiver_phone',
        'receiver_address',
        'receiver_department_id',
        'receiver_country_id',
        'delivery_purpose',
        'delivery_type',
        'delivery_extra_cost',
        'delivery_extra_cost_tax',
        'transport_type',
        'transport_company',
        'notes',
        'coupon_id',
        'coupon_name',
        'coupon_value',
        'coupon_date',
        'coupon_currency'
    ];

    public function items() {
        return $this->hasMany(OrderItem::class, 'order_number', 'order_number');
    }
}
