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
        'trade_id',
        'nit',
        'email',
        'token_production',
        'token_stage',
        'usd_sales_goal',
        'is_active',
    ];
}
