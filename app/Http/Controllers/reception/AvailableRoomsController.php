<?php

namespace App\Http\Controllers\reception;

use App\Http\Controllers\Controller;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AvailableRoomsController extends Controller
{
    public function filter(Request $request)
    {
        $checkInDate = $request->input('checkInDate');
        $checkOutDate = $request->input('checkOutDate');

        // Validate the dates
        $request->validate([
            'checkInDate' => 'required|date|before:checkOutDate',
            'checkOutDate' => 'required|date|after:checkInDate',
        ]);

        try {
            // Parse the dates to ensure they are valid Carbon instances
            $checkInDate = Carbon::parse($checkInDate);
            $checkOutDate = Carbon::parse($checkOutDate);

            // Log the search date range
            $searchDays = [];
            $searchDay = $checkInDate->clone();
            while ($searchDay->lte($checkOutDate)) {
                $searchDays[] = $searchDay->toDateString();
                $searchDay->addDay();
            }

            // Log room occupancy and build occupancy array
            $roomOccupancies = [];
            $allRooms = Rooms::all();
            foreach ($allRooms as $room) {
                $bookings = $room->bookings;
                if ($bookings->isNotEmpty()) {
                    foreach ($bookings as $booking) {
                        $bookingCheckIn = Carbon::parse($booking->check_in_date);
                        $bookingCheckOut = Carbon::parse($booking->check_out_date);
                        $numberOfDays = $bookingCheckIn->diffInDays($bookingCheckOut);

                        $occupiedDays = [];
                        for ($i = 0; $i <= $numberOfDays; $i++) {
                            $occupiedDays[] = $bookingCheckIn->clone()->addDays($i)->toDateString();
                        }

                        $roomOccupancies[$room->room_id] = array_merge($roomOccupancies[$room->room_id] ?? [], $occupiedDays);
                    }
                } else {
                    $roomOccupancies[$room->room_id] = [];
                }
            }

            // Get all available rooms
            $allAvailableRooms = Rooms::where('is_available', true)->get();

            $availableRooms = $allAvailableRooms->filter(function ($room) use ($searchDays, $roomOccupancies) {
                if (isset($roomOccupancies[$room->room_id]) && !empty($roomOccupancies[$room->room_id])) {
                    $overlap = array_intersect($searchDays, $roomOccupancies[$room->room_id]);
                    return empty($overlap);
                }
                return true;
            });

            // Ensure the response is an array by resetting the keys
            return response()->json($availableRooms->values());
        } catch (\Exception $e) {
            Log::error('Error fetching available rooms', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'An error occurred while fetching available rooms.'], 500);
        }
    }
}
