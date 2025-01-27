<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Code extends Model
{
    protected $table = 'codes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code_type',
        'item',
        'name'
    ];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];
}
