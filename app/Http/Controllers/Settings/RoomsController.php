<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\RoomsUpdateRequest;
use App\Models\Rooms;
use App\Models\RoomTypes;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class RoomsController extends Controller
{
    /**
     * Show the rooms settings page.
     */
    public function index(Request $request): Response
    {
        $rooms = Rooms::with('roomType', 'currency')->get();
        $roomTypes = RoomTypes::all();
        $currencies = Currency::all();
        $baseCurrency = Currency::where('is_base_currency', true)->first();

        return Inertia::render('management/settings/rooms', [
            'rooms' => $rooms,
            'roomTypes' => $roomTypes,
            'currencies' => $currencies,
            'baseCurrency' => $baseCurrency,
        ]);
    }

    /**
     * Store a new room.
     */
    public function store(RoomsUpdateRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $validatedData['is_available'] = true;

        // Set the currency_id to the base currency
        $baseCurrency = Currency::where('is_base_currency', true)->first();
        $validatedData['currency_id'] = $baseCurrency->currency_id;

        $room = Rooms::create($validatedData);

        $primaryKey = $room->getKeyName();
        $timestamps = ['created_at', 'updated_at'];

        foreach ($validatedData as $key => $value) {
            if ($key !== $primaryKey && !in_array($key, $timestamps)) {
                AuditLog::create([
                    'table_name' => 'rooms',
                    'record_id' => $room->room_id,
                    'action' => 'INSERT',
                    'new_value' => $value,
                    'changed_by' => Auth::user()->username,
                    'column_affected' => $key,
                ]);
            }
        }

        return to_route('management.settings.rooms.index');
    }

    /**
     * Update an existing room.
     */
    public function update(RoomsUpdateRequest $request, $room_id): RedirectResponse
    {
        $room = Rooms::find($room_id);

        if (!$room) {
            return redirect()->route('management.settings.rooms.index')->with('error', 'Room not found.');
        }

        $oldValues = $room->toArray();
        $primaryKey = $room->getKeyName();
        $timestamps = ['created_at', 'updated_at'];

        $room->update($request->validated());

        foreach ($request->validated() as $key => $newValue) {
            if ($key !== $primaryKey && !in_array($key, $timestamps) && $oldValues[$key] !== $newValue) {
                AuditLog::create([
                    'table_name' => 'rooms',
                    'record_id' => $room->room_id,
                    'action' => 'UPDATE',
                    'old_value' => $oldValues[$key],
                    'new_value' => $newValue,
                    'changed_by' => Auth::user()->username,
                    'column_affected' => $key,
                ]);
            }
        }

        return redirect()->route('management.settings.rooms.index')->with('success', 'Room updated successfully.');
    }

    /**
     * Delete a room.
     */
    public function destroy(Request $request, $room_id): RedirectResponse
    {
        $request->validate([
            'password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail('The password is incorrect.');
                    }
                },
            ],
        ]);

        $room = Rooms::find($room_id);

        if (!$room) {
            return redirect()->route('management.settings.rooms.index')->with('error', 'Room not found.');
        }

        $oldValues = $room->toArray();
        $primaryKey = $room->getKeyName();
        $timestamps = ['created_at', 'updated_at'];

        $room->delete();

        foreach ($oldValues as $key => $value) {
            if ($key !== $primaryKey && !in_array($key, $timestamps)) {
                AuditLog::create([
                    'table_name' => 'rooms',
                    'record_id' => $room_id,
                    'action' => 'DELETE',
                    'old_value' => $value,
                    'changed_by' => Auth::user()->username,
                    'column_affected' => $key,
                ]);
            }
        }

        return redirect()->route('management.settings.rooms.index')->with('success', 'Room deleted successfully.');
    }
}
