<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $table = 'order_items';
    //protected $primaryKey = 'part_num';
    protected $fillable = [
        'order_number',
        'item',
        'part_num',
        'trade_part_num',
        'product_name',
        'brand',
        'quantity',
        'sed_unit_price',
        'sed_total_price',
        'sed_tax_value',
        'is_tax_applied',
        'currency'
    ];
    public function order() {
        return $this->belongsTo(Order::class);
    }
}
