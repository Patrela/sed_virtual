<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImported extends Model
{
    use HasFactory;
    protected $table = 'products_imported';
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
        'price_tax_status',
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
        'is_new'
    ];
}
