<?php

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'currency_code' => ['required', 'string', 'max:255', Rule::unique('currencies', 'currency_code')->ignore($this->route('currency_id'), 'currency_id')],
            'currency_name' => ['required', 'string', 'max:255'],
            'exchange_rate' => ['required', 'numeric'],
            'is_base_currency' => ['boolean'],
        ];
    }
}
