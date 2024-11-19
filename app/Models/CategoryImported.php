<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class CategoryImported extends Model
{
    protected $table = 'categories_imported';
    protected $primaryKey = 'id_category';
    protected $fillable = [
        'id',
        'name',
        'slug',
        'group_name',
        'parent_id',
        'item_like',
        'is_new'
    ];
    protected $hidden = [
        'id_category'
    ];

}

