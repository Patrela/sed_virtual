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
        'order',
        'item',
        'part_num',
        'name',
        'quantity',
        'unit_price',
        'total_price'
    ];
    public function order() {
        return $this->belongsTo(Order::class);
    }
}
