<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\CurrencyUpdateRequest;
use App\Models\Currency;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

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

        if (Currency::count() == 0) {
            $validatedData['is_base_currency'] = true;
        } else {
            $validatedData['is_base_currency'] = false;
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

        Log::info('Current Currency Data:', $currency->toArray());
        Log::info('Incoming Data:', $validatedData);

        if (isset($validatedData['is_base_currency']) && $validatedData['is_base_currency']) {
            Currency::where('is_base_currency', true)->update(['is_base_currency' => false]);
        }

        $currency->update($validatedData);

        Log::info('Updated Currency Data:', $currency->fresh()->toArray());
        Log::info('All Currencies:', Currency::all()->toArray());

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
