<?php

namespace App\Http\Controllers\reception;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Rooms;
use App\Models\RoomTypes;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BookingsController extends Controller
{
    public function create()
    {
        $roomTypes = RoomTypes::all();
        $rooms = Rooms::where('is_available', true)->get();
        return Inertia::render('reception/bookings', [
            'roomTypes' => $roomTypes,
            'rooms' => $rooms,
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Booking request received', ['request' => $request->all()]);

        try {
            $validatedData = $request->validate([
                'nationalId' => 'required|string|max:255',
                'guestName' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'phone' => 'required|string|max:15',
                'homeAddress' => 'required|string|max:255',
                'selectedRooms' => 'required|array',
                'selectedRooms.*.roomNumber' => 'required|exists:rooms,room_number',
                'checkInDate' => 'required|date',
                'checkOutDate' => 'required|date|after:checkInDate',
                'price' => 'required|numeric',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request' => $request->all(),
            ]);
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        // Get the role ID for the "Guest" role
        $guestRole = Roles::where('role_name', 'Guest')->firstOrFail();

        // Update or create the guest user
        $guest = User::updateOrCreate(
            ['national_id_number' => $validatedData['nationalId']],
            [
                'first_name' => $validatedData['guestName'],
                'last_name' => $validatedData['surname'],
                'email' => $validatedData['email'],
                'phone_number' => $validatedData['phone'],
                'address' => $validatedData['homeAddress'],
                'role_id' => $guestRole->role_id,
            ]
        );

        // Create the booking for each selected room
        foreach ($validatedData['selectedRooms'] as $selectedRoom) {
            Booking::create([
                'room_id' => Rooms::where('room_number', $selectedRoom['roomNumber'])->first()->room_id,
                'guest_id' => $guest->id,
                'check_in' => $validatedData['checkInDate'],
                'check_out' => $validatedData['checkOutDate'],
                'total_cost' => $validatedData['price'],
                'is_paid' => false,
                'booker_id' => auth()->id(),
            ]);
        }

        return response()->json(['message' => 'Booking successful'], 200);
    }

    public function roomsAndTypes()
    {
        $roomTypes = RoomTypes::all();
        $rooms = Rooms::where('is_available', true)->get();
        return response()->json([
            'roomTypes' => $roomTypes,
            'rooms' => $rooms,
        ]);
    }

    public function suggestions(Request $request)
    {
        $query = $request->query('query');
        $users = User::where('national_id_number', 'like', "%{$query}%")
            ->orWhere('phone_number', 'like', "%{$query}%")
            ->get(['id', 'first_name', 'last_name', 'national_id_number', 'phone_number', 'email', 'address']);
        return response()->json($users);
    }
}
