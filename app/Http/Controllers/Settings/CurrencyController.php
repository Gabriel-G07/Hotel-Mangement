<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\CurrencyUpdateRequest;
use App\Models\Currency;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class CurrencyController extends Controller
{
    /**
     * Show the currencies settings page.
     */
    public function index(Request $request): Response
    {
        $currencies = Currency::all();

        return Inertia::render('management/settings/currencies', [
            'currencies' => $currencies,
        ]);
    }

    /**
     * Store a new currency.
     */
    public function store(CurrencyUpdateRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        if ($validatedData['is_base_currency']) {
            Currency::where('is_base_currency', true)->update(['is_base_currency' => false]);
        } else {
            if (Currency::where('is_base_currency', true)->count() == 0) {
                $validatedData['is_base_currency'] = true;
            }
        }

        Currency::create($validatedData);

        return to_route('management.settings.currencies.index');
    }

    /**
     * Update an existing currency.
     */
    public function update(CurrencyUpdateRequest $request, $currency_id): RedirectResponse
    {
        $currency = Currency::find($currency_id);

        if (!$currency) {
            return redirect()->route('management.settings.currencies.index')->with('error', 'Currency not found.');
        }

        $validatedData = $request->validated();

        if ($validatedData['is_base_currency']) {
            Currency::where('is_base_currency', true)->update(['is_base_currency' => false]);
        }

        $currency->update($validatedData);

        return redirect()->route('management.settings.currencies.index')->with('success', 'Currency updated successfully.');
    }

    /**
     * Delete a currency.
     */
    public function destroy($currency_id): RedirectResponse
    {
        $currency = Currency::find($currency_id);

        if (!$currency) {
            return redirect()->route('management.settings.currencies.index')->with('error', 'Currency not found.');
        }

        $currency->delete();

        return redirect()->route('management.settings.currencies.index')->with('success', 'Currency deleted successfully.');
    }
}
