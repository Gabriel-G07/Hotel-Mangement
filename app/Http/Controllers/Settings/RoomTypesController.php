<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\RoomTypesUpdateRequest;
use App\Models\RoomTypes;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class RoomTypesController extends Controller
{
    /**
     * Show the room types settings page.
     */
    public function index(Request $request): Response
    {
        $roomTypes = RoomTypes::all();

        return Inertia::render('management/settings/room_types', [
            'roomTypes' => $roomTypes,
        ]);
    }

    /**
     * Store a new room type.
     */
    public function store(RoomTypesUpdateRequest $request): RedirectResponse
    {
        RoomTypes::create($request->validated());

        return to_route('management.settings.room_types.index');
    }

    /**
     * Update an existing room type.
     */
    public function update(RoomTypesUpdateRequest $request, $room_type_id): RedirectResponse
    {
        $roomType = RoomTypes::find($room_type_id);

        if (!$roomType) {
            return redirect()->route('management.settings.room_types.index')->with('error', 'Room type not found.');
        }

        $roomType->update($request->validated());

        return redirect()->route('management.settings.room_types.index')->with('success', 'Room type updated successfully.');
    }

    /**
     * Delete a room type.
     */
    public function destroy($room_type_id): RedirectResponse
    {
        $roomType = RoomTypes::find($room_type_id);

        if (!$roomType) {
            return redirect()->route('management.settings.room_types.index')->with('error', 'Room type not found.');
        }

        $roomType->delete();

        return redirect()->route('management.settings.room_types.index')->with('success', 'Room type deleted successfully.');
    }
}
