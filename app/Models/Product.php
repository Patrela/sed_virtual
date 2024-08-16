<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $primaryKey = 'part_num';
    protected $keyType = 'string'; // Especificamos que la clave primaria es de tipo string
    public $incrementing = false;
    protected $fillable = [
        'id_provider',
        'sku',
        'part_num',
        'name',
        'slug',
        'description',
        'short_description',
        'stock_quantity',
        'unit',
        'guarantee',
        'regular_price',
        'sale_price',
        'tax_status',
        'currency',
        'department',
        'category',
        'segment',
        'brand',
        'attributes',
        'dimension_length',
        'dimension_width',
        'dimension_height',
        'dimension_weight',
        'image_1',
        'image_2',
        'image_3',
        'image_4',
        'contact_unit',
        'contact_agent',
        'contact_email',
        'is_insale',
        'is_sold',
        'is_discontinued',
        'is_reviewed',
        'is_reserved',
        'url_affinity'
    ];
    protected $hidden = [ 'created_at', 'updated_at' ];
    public function Provider() {
        return $this->id_provider; //$this->hasOne(SedProductCategory::class);
    }
    public function IsProviderProduct($provider, $part_num) {
        return Product::where('id_provider', $provider)
                    ->where('part_num', $part_num)
                    ->exists(); //$this->hasOne(SedProductCategory::class);
    }
/*
    public function categories() {
        return $this->hasMany(SedProductCategory::class, 'part_num', 'part_num');
    }

    public function attributes() {
        return $this->hasMany(SedProductAttribute::class, 'part_num', 'part_num');
    }
    */
}
