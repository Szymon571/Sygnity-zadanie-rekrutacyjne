@props([
    'availableCurrencies',
    'searchedCurrency' => null,
    'searchedDate' => null,
    'searchedRate' => null,
    'searchError' => null,
])

@php
    $searchedCurrencyOption = $searchedCurrency !== null
        ? $availableCurrencies->firstWhere('code', $searchedCurrency)
        : null;
    $searchedCurrencyLabel = $searchedCurrencyOption !== null
        ? $searchedCurrencyOption['code'].' - '.$searchedCurrencyOption['currency']
        : '';
@endphp

<div class="min-w-0 rounded-2xl border border-zinc-200 bg-zinc-50/80 p-5 dark:border-zinc-700 dark:bg-zinc-950/70">
    <h2 class="text-xs font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">
        {{ __('Rate search') }}
    </h2>

    <form method="GET" action="{{ route('dashboard') }}" class="mt-4 grid gap-3 md:grid-cols-[minmax(0,1fr)_auto]">
        <div class="md:col-span-2">
            <x-dashboard.currency-combobox
                id="currency_search"
                name="currency"
                :label="__('Currency')"
                :currencies="$availableCurrencies"
                :selected-code="$searchedCurrency ?? ''"
                :query="$searchedCurrencyLabel"
                option-prefix="rate-currency-option"
            />
        </div>

        <div>
            <label for="date" class="mb-2 block text-sm font-medium text-zinc-900 dark:text-zinc-50">
                {{ __('Date') }}
            </label>
            <input
                id="date"
                name="date"
                type="date"
                max="{{ now()->toDateString() }}"
                value="{{ $searchedDate ?? now()->toDateString() }}"
                @error('date') aria-invalid="true" aria-describedby="date_error" @enderror
                class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-sm text-zinc-900 outline-none transition focus:border-zinc-500 focus:ring-2 focus:ring-zinc-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700"
            />
            @error('date')
                <p id="date_error" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="self-end">
            <button
                type="submit"
                class="inline-flex w-full items-center justify-center rounded-xl bg-zinc-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-700 md:min-w-[11rem] dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-300"
            >
                {{ __('Check rate') }}
            </button>
        </div>
    </form>

    <section
        class="mt-3 flex min-h-32 items-center rounded-2xl border p-4"
        aria-live="polite"
    >
        @if ($searchError !== null)
            <div class="w-full rounded-xl border border-red-200 bg-red-50/80 p-3 dark:border-red-900/70 dark:bg-red-950/40">
                <p class="text-sm font-medium text-red-700 dark:text-red-300">
                    {{ __('Rate unavailable') }}
                </p>
                <p class="mt-1 text-sm leading-6 text-red-600 dark:text-red-400">
                    {{ $searchError }}
                </p>
            </div>
        @elseif ($searchedRate !== null)
            <div class="w-full">
                <p class="text-sm font-semibold tracking-wide text-zinc-900 dark:text-zinc-50">
                    {{ $searchedRate['code'] }} - {{ $searchedRate['currency'] }}
                </p>
                <p class="mt-2 text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-50">
                    {{ number_format($searchedRate['rate'], 4, ',', ' ') }} PLN
                </p>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                    {{ __('Date :date.', ['date' => $searchedRate['date']]) }}
                </p>
            </div>
        @else
            <div class="w-full">
                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50">
                    {{ __('Rate result') }}
                </p>
                <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                    {{ __('Choose a currency and date to see the NBP rate here.') }}
                </p>
            </div>
        @endif
    </section>
</div>
