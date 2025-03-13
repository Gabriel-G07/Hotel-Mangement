<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_code',
        'currency_name',
        'exchange_rate',
        'is_base_currency',
    ];

    protected $table = 'currencies';
    protected $primaryKey = 'currency_id';
    public $incrementing = true;
}
