<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;
class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id_category';
    protected $fillable = [
        'id',
        'name',
        'slug',
        'group_name',
        'parent_id'
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

