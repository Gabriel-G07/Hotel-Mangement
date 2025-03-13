<?php

namespace App\Http\Requests\Settings;

use App\Models\Rooms;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomsUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'room_number' => ['nullable', 'string', 'max:255'],
            'room_type_id' => ['required', 'exists:room_types,room_type_id'],
            'price_per_night' => ['required', 'string', 'max:255'],
            'currency_id' => ['required', 'exists:currencies,currency_id'],
        ];
    }
}
