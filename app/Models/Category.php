<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id_category';
    protected $fillable = [
        'id',
        'name',
        'slug',
        'group_name',
        'parent_id',
        'item_like'
    ];
    protected $hidden = [
        'id_category',
        'created_at',
        'updated_at'
    ];

    // public function scopeDepartment(Builder $builder): void
    // {
    //     $builder->where('group_name','departamento');
    // }
    // public function scopeDepartmentCategories(Builder $builder): void
    // {
    //     $builder->where('group_name','categoria')
    //             ->where('parent_id',$this->id);
    // }
}

