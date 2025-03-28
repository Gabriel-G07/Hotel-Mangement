<?php

namespace App\Http\Requests\reception;

use Illuminate\Foundation\Http\FormRequest;

class BookingUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'guestNationalId' => 'required|string|max:255',
            'guestName' => 'required|string|max:255',
            'guestSurname' => 'required|string|max:255',
            'guestemail' => 'required|email|max:255',
            'guestphone' => 'required|string|max:255',
            'guestAddress' => 'required|string|max:255',
            'selectedRooms' => 'required|array',
            'checkInDate' => 'required|date',
            'checkOutDate' => 'required|date|after:checkInDate',
            'bookingType' => 'required|string|in:Self,Other',
            'bookerNationalId' => 'nullable|string|max:255|required_if:bookingType,Other',
            'bookerName' => 'nullable|string|max:255|required_if:bookingType,Other',
            'bookerSurname' => 'nullable|string|max:255|required_if:bookingType,Other',
            'bookeremail' => 'nullable|email|max:255|required_if:bookingType,Other',
            'bookerphone' => 'nullable|string|max:255|required_if:bookingType,Other',
        ];
    }
}
