<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomTypes extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_name',
        'description',
    ];

    protected $table = 'room_types';
    protected $primaryKey = 'room_type_id';
    public $incrementing = true;
}
