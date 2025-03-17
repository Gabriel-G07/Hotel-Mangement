<?php

namespace App\Http\Controllers\management;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Inertia\Inertia;

class BookingsController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('room', 'guest', 'receptionist')->get();
        return Inertia::render('management/bookings', ['bookings' => $bookings]);
    }
}
