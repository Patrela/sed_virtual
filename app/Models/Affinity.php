<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Affinity extends Model
{
    protected $table = 'affinities';
    protected $primaryKey = 'id';
    protected $fillable = [
        'brand_name',
        'program',
        'program_url',
        'program_image',
        'is_program_active'
    ];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];
}
