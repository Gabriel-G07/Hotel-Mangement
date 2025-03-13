<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'guest_id',
        'check_in',
        'check_out',
        'total_cost',
        'is_paid'
    ];

    public function room()
    {
        return $this->belongsTo(Rooms::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
