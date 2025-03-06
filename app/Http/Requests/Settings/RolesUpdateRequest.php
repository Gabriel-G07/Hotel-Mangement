<?php

namespace App\Http\Requests\Settings;

use App\Models\Roles;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RolesUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Roles::class, 'role_name')->ignore($this->input('role_id'), 'role_id'),
            ],
            'description' => ['required', 'string'],
        ];
    }
}
