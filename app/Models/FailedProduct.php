<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FailedProduct extends Model
{
    use HasFactory;
    protected $table = 'product_nonimported';

    protected $fillable = [
        'part_num',
        'error_description'    ];
    protected $hidden = [ 'created_at', 'updated_at' ];
}
