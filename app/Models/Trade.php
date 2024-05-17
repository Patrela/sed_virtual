<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;
    protected $table = 'trades';
    //protected $primaryKey = 'part_num';
    protected $fillable = [
        'name',
        'email',
        'trade_id',
        'nit',
        'is_active',
    ];
}
