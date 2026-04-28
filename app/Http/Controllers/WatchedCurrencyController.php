<?php

namespace App\Http\Controllers;

use App\Models\WatchedCurrency;
use App\Services\NbpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WatchedCurrencyController
{
    public function store(Request $request, NbpService $nbpService): RedirectResponse
    {
        $request->merge([
            'currency_code' => strtoupper((string) $request->input('currency_code')),
        ]);

        $availableCurrencyCodes = $nbpService->getAvailableCurrencies()
            ->pluck('code')
            ->all();

        $validated = $request->validate([
            'currency_code' => [
                'required',
                'string',
                'size:3',
                Rule::in($availableCurrencyCodes),
                Rule::unique('watched_currencies', 'currency_code')->where('user_id', $request->user()->id),
            ],
        ]);

        $request->user()->watchedCurrencies()->create([
            'currency_code' => $validated['currency_code'],
        ]);

        return to_route('dashboard')->with([
            'status' => __('Currency added to the watchlist.'),
            'status_variant' => 'added',
        ]);
    }

    public function destroy(Request $request, WatchedCurrency $watchedCurrency): RedirectResponse
    {
        abort_unless($watchedCurrency->user_id === $request->user()->id, 404);

        $watchedCurrency->delete();

        return to_route('dashboard')->with([
            'status' => __('Currency removed from the watchlist.'),
            'status_variant' => 'removed',
        ]);
    }
}
