<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_categories';
    protected $primaryKey = 'id_category';
    protected $fillable = [
        'part_num',
        'id',
        'name',
        'slug',
        'group_name',
        'parent_id'
    ];
    protected $hidden = [ 'created_at', 'updated_at' ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
