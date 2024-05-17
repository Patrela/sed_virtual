<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $table = 'product_attributes';
    protected $primaryKey = 'id_attribute';
    protected $fillable = [
        'part_num',
        'id',
        'name',
        'value'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
