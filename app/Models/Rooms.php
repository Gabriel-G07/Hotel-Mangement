<?php

// app/Models/Rooms.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_type_id',
        'price_per_night',
        'currency_id',
    ];

    protected $table = 'rooms';
    protected $primaryKey = 'room_id';
    public $incrementing = true;

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomTypes::class, 'room_type_id');
    }
}
