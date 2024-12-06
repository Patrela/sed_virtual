<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class UserVisitLog extends Model
{
    protected $table = 'users_visit_log';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'trade_id',
        'log_date'
    ];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];

}

