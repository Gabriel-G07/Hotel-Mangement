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
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();
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
                $room = Rooms::where('room_number', $roomNumber)->lockForUpdate()->first(); // 👈 Add lock

                if ($room) {
                    $bookingStatus = $validatedData['checkInDate'] === date('Y-m-d') ? 'Confirmed' : 'Pending';

                    try {
                        // Create booking FIRST
                        Booking::create([
                            'room_id' => $room->room_id,
                            'guest_id' => $guest->id,
                            'check_in_date' => $validatedData['checkInDate'],
                            'check_out_date' => $validatedData['checkOutDate'],
                            'grand_total' => $room->price_per_night,
                            'currency_id' => $room->currency_id,
                            'booking_status' => $bookingStatus,
                            'booker_id' => $guest->id,
                            'booked_by' => Auth::id(),
                            'booked_from' => 'reception',
                        ]);

                        // Then update room status
                        $room->update(['is_available' => false]); // 👈 Use update() instead of save()
                    } catch (\Exception $e) {
                        Log::error('Failed to save booking', [
                            'room_id' => $room->room_id,
                            'guest_id' => $guest->id,
                            'check_in_date' => $validatedData['checkInDate'],
                            'check_out_date' => $validatedData['checkOutDate'],
                            'grand_total' => $room->price_per_night,
                            'currency_id' => $room->currency_id,
                            'booking_status' => $bookingStatus,
                            'booker_id' => $guest->id,
                            'booked_by' => Auth::id(),
                            'booked_from' => 'reception',
                            'error' => $e->getMessage()
                        ]);
                        throw $e; // Re-throw the exception to trigger rollback
                    }
                }
            }

            DB::commit(); // 👈 Only commit if everything succeeds
            return to_route('reception.bookings');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Booking failed: '.$e->getMessage()]);
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
