<?php

namespace App\Http\Controllers\reception;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Rooms;
use App\Models\RoomTypes;
use App\Models\User;
use App\Models\Roles;
use App\Models\BookedByDetails;
use App\Models\BookerDetails;
use App\Models\Currency;
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
            $guest = User::whereHas('role', function ($query) {
                $query->where('role_name', 'Guest');
            })->where(function ($query) use ($validatedData) {
                $query->where('national_id_number', $validatedData['guestNationalId'])
                      ->orWhere('email', $validatedData['guestemail']);
            })->first();

            if ($guest) {
                Log::info('Guest found', ['guest' => $guest]);
            } else {
                Log::info('Guest not found, creating guest');
                $guestRole = Roles::where('role_name', 'Guest')->firstOrFail();
                $randomPassword = Hash::make(Str::random(10));

                $guest = User::create([
                    'username' => strtolower($validatedData['guestName'] . '.' . $validatedData['guestSurname']),
                    'national_id_number' => $validatedData['guestNationalId'],
                    'first_name' => $validatedData['guestName'],
                    'last_name' => $validatedData['guestSurname'],
                    'email' => $validatedData['guestemail'],
                    'phone_number' => $validatedData['guestphone'],
                    'address' => $validatedData['guestAddress'],
                    'role_id' => $guestRole->role_id,
                    'password' => $randomPassword,
                ]);
                Log::info('Guest created successfully', ['guest' => $guest]);
            }

            // Create booking for each selected room
            foreach ($validatedData['selectedRooms'] as $roomNumber) {
                $room = Rooms::where('room_number', $roomNumber)->first();

                if ($room) {
                    Log::info('Room found', ['room' => $room]);
                    $bookingStatus = $validatedData['checkInDate'] === date('Y-m-d') ? 'Confirmed' : 'Pending';

                    // Parse the dates
                    $checkInDate = Carbon::parse($validatedData['checkInDate']);
                    $checkOutDate = Carbon::parse($validatedData['checkOutDate']);

                    // Explicitly check if checkOutDate is before checkInDate
                    if ($checkOutDate->lessThanOrEqualTo($checkInDate)) {
                        Log::error('Invalid date range: Check-out date must be after check-in date.', [
                            'checkInDate' => $checkInDate,
                            'checkOutDate' => $checkOutDate,
                        ]);
                        return redirect()->back()->withErrors(['error' => 'Check-out date must be after check-in date.']);
                    }

                    // Calculate the number of days between check-in and check-out
                    $numberOfDays = $checkOutDate->diffInDays($checkInDate);

                    // Calculate the grand total
                    $grandTotal = $room->price_per_night * $numberOfDays;

                    // Create the booking
                    Booking::create([
                        'room_id' => $room->room_id,
                        'guest_id' => $guest->id,
                        'check_in_date' => $validatedData['checkInDate'],
                        'check_out_date' => $validatedData['checkOutDate'],
                        'grand_total' => $grandTotal,
                        'currency_id' => $room->currency_id,
                        'booking_status' => $bookingStatus,
                        'booked_by_id' => Auth::user()->id,
                        'booker_id' => $guest->id,
                        'booked_from' => 'reception',
                    ]);

                    $room->is_available = false;
                    $room->save();
                    Log::info('Room booking created and marked as unavailable', ['room' => $room]);
                } else {
                    Log::warning('Room not found', ['roomNumber' => $roomNumber]);
                }
            }

            return to_route('reception.bookings');
        } catch (\Exception $e) {
            Log::error('Error processing booking', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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
