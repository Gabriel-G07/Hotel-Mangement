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
        'check_in_date',
        'check_out_date',
        'grand_total',
        'currency_id',
        'booking_status',
        'booker_id',
        'booked_from',
        'booked_by'
    ];

    public function room()
    {
        return $this->belongsTo(Rooms::class, 'room_id', 'room_id');
    }

    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_id', 'id');
    }

    public function bookingInforment()
    {
        return $this->belongsTo(User::class, 'booker_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'currency_id');
    }

    public function staffMember()
    {
        return $this->belongsTo(User::class, 'booked_by', 'id');
    }
}
