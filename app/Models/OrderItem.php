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
        'product_name',
        'brand',
        'quantity',
        'unit_price',
        'total_price',
        'tax_value',
        'currency'
    ];
    public function order() {
        return $this->belongsTo(Order::class);
    }
}
