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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\reception\BookingUpdateRequest;
use Carbon\Carbon;

class BookingsController extends Controller
{
    public function index()
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Guest');
        })->get(['id', 'first_name', 'last_name', 'national_id_number', 'phone_number', 'email', 'address']);
        $availableRoomTypes = RoomTypes::all();
        $availableRooms = Rooms::where('is_available', true)->get();
        return Inertia::render('reception/bookings', [
            'availableRoomTypes' => $availableRoomTypes,
            'availableRooms' => $availableRooms,
            'availableGuests' => $users,
        ]);
    }

    public function store(BookingUpdateRequest $request): RedirectResponse
    {
        try {
            Log::info('Booking request received', ['request' => $request->all()]);

            $validatedData = $request->validated();
            Log::info('Validation successful', ['validatedData' => $validatedData]);

            // Find or create guest
            $guest = User::where('national_id_number', $validatedData['guestNationalId'])
                ->orWhere('email', $validatedData['guestemail'])
                ->first();

            if (!$guest) {
                Log::info('Guest not found, creating new guest', ['guestData' => $validatedData]);
                $guestRole = Roles::where('role_name', 'Guest')->firstOrFail();
                $guest = User::create([
                    'username' => strtolower($validatedData['guestemail']),
                    'national_id_number' => $validatedData['guestNationalId'],
                    'first_name' => $validatedData['guestName'],
                    'last_name' => $validatedData['guestSurname'],
                    'email' => $validatedData['guestemail'],
                    'phone_number' => $validatedData['guestphone'],
                    'address' => $validatedData['guestAddress'],
                    'role_id' => $guestRole->role_id,
                    'password' => Hash::make(Str::random(10)),
                ]);
                Log::info('Guest created successfully', ['guest' => $guest]);
            } else {
                Log::info('Guest found', ['guest' => $guest]);
            }

            // Handle booking type
            $bookerId = $guest->id; // Default to guest ID for "Self"
            if ($validatedData['bookingType'] === 'Other') {
                $booker = User::where('national_id_number', $validatedData['bookerNationalId'])
                    ->orWhere('email', $validatedData['bookeremail'])
                    ->first();

                if (!$booker) {
                    Log::info('Booker not found, creating new booker', ['bookerData' => $validatedData]);
                    $guestRole = Roles::where('role_name', 'Guest')->firstOrFail();
                    $booker = User::create([
                        'username' => strtolower($validatedData['bookeremail']),
                        'national_id_number' => $validatedData['bookerNationalId'],
                        'first_name' => $validatedData['bookerName'],
                        'last_name' => $validatedData['bookerSurname'],
                        'email' => $validatedData['bookeremail'],
                        'phone_number' => $validatedData['bookerphone'],
                        'role_id' => $guestRole->role_id,
                        'password' => Hash::make(Str::random(10)),
                    ]);
                    Log::info('Booker created successfully', ['booker' => $booker]);
                } else {
                    Log::info('Booker found', ['booker' => $booker]);
                }

                $bookerId = $booker->id;
            }

            // Create booking
            foreach ($validatedData['selectedRooms'] as $roomNumber) {
                $room = Rooms::where('room_number', $roomNumber)->first();
                if ($room) {
                    Log::info('Room found for booking', ['room' => $room]);
                    $booking = Booking::create([
                        'room_id' => $room->room_id,
                        'guest_id' => $guest->id,
                        'check_in_date' => $validatedData['checkInDate'],
                        'check_out_date' => $validatedData['checkOutDate'],
                        'grand_total' => $room->price_per_night * Carbon::parse($validatedData['checkOutDate'])->diffInDays(Carbon::parse($validatedData['checkInDate'])),
                        'currency_id' => $room->currency_id,
                        'booking_status' => $validatedData['checkInDate'] === date('Y-m-d') ? 'Confirmed' : 'Pending',
                        'booked_by_id' => Auth::user()->id,
                        'booker_id' => $bookerId,
                        'booked_from' => 'reception',
                    ]);
                    Log::info('Booking created successfully', ['booking' => $booking]);

                    $room->is_available = false;
                    $room->save();
                    Log::info('Room availability updated', ['room' => $room]);
                } else {
                    Log::warning('Room not found for booking', ['roomNumber' => $roomNumber]);
                }
            }

            return to_route('reception.bookings');
        } catch (\Exception $e) {
            Log::error('Error processing booking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to process booking.']);
        }
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
