<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeImported extends Model
{
    use HasFactory;
    protected $table = 'trades_imported';
    //protected $primaryKey = 'part_num';
    protected $fillable = [
        'name',
        'nit',
        'trade_id',
        'is_new',
    ];
}
