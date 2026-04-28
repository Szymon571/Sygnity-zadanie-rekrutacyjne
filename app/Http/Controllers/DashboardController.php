<?php

namespace App\Http\Controllers;

use App\Services\NbpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class DashboardController
{
    public function index(Request $request, NbpService $nbpService): View
    {
        $user = Auth::user();
        $availableCurrencies = $nbpService->getAvailableCurrencies();
        $availableCurrenciesByCode = $availableCurrencies->keyBy('code');
        $validated = Validator::make($request->query(), [
            'currency' => ['nullable', 'required_with:date', 'string', 'size:3', Rule::in($availableCurrenciesByCode->keys()->all())],
            'date' => ['nullable', 'required_with:currency', 'date', 'before_or_equal:today'],
        ])->validate();

        $watchedCurrencies = $user->watchedCurrencies()
            ->get()
            ->map(function ($watchedCurrency) use ($availableCurrenciesByCode) {
                $currency = $availableCurrenciesByCode->get($watchedCurrency->currency_code);

                return [
                    'id' => $watchedCurrency->id,
                    'code' => $watchedCurrency->currency_code,
                    'currency' => $currency['currency'] ?? null,
                    'table' => $currency['table'] ?? null,
                    'rate' => $currency['rate'] ?? null,
                    'created_at' => $watchedCurrency->created_at,
                ];
            });

        $goldPrices = $nbpService->getLastGoldPrices();
        $searchedRate = null;
        $searchedCurrency = $validated['currency'] ?? null;
        $searchedDate = $validated['date'] ?? null;
        $searchError = null;

        if ($searchedCurrency !== null && $searchedDate !== null) {
            $currency = $availableCurrenciesByCode->get($searchedCurrency);

            $searchedRate = $nbpService->getHistoricalRate(
                table: $currency['table'],
                code: $searchedCurrency,
                date: $searchedDate,
            );

            if ($searchedRate === null) {
                $searchError = __('NBP has no published rate for the selected currency on the chosen date.');
            }
        }

        return view('dashboard', [
            'watchedCurrencies' => $watchedCurrencies,
            'availableCurrencies' => $availableCurrencies,
            'goldPrices' => $goldPrices,
            'searchedRate' => $searchedRate,
            'searchedCurrency' => $searchedCurrency,
            'searchedDate' => $searchedDate,
            'searchError' => $searchError,
        ]);
    }
}
