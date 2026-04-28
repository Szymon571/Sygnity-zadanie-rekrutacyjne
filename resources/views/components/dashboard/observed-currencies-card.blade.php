@props([
    'watchedCurrencies',
    'availableCurrencies',
])

<section class="min-w-0 rounded-2xl border border-neutral-200 bg-white p-5 shadow-xs dark:border-neutral-700 dark:bg-neutral-900">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h2 class="text-sm font-medium text-zinc-900 dark:text-zinc-50">{{ __('Observed currencies') }}</h2>
            <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                {{ __('Add currencies to the watchlist and track their latest NBP rates here.') }}
            </p>
        </div>
        <span class="shrink-0 self-start whitespace-nowrap rounded-full border border-zinc-200 px-2.5 py-1 text-xs font-medium text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
           {{ __('Observed currencies: ') . $watchedCurrencies->count() }}
        </span>
    </div>

    <form method="POST" action="{{ route('watched-currencies.store') }}" class="mt-5 grid gap-3 md:grid-cols-[minmax(0,1fr)_auto]">
        @csrf

        <x-dashboard.currency-combobox
            id="currency_code_search"
            name="currency_code"
            :label="__('Currency to observe')"
            :currencies="$availableCurrencies"
            :selected-code="old('currency_code', '')"
            query=""
            option-prefix="watch-currency-option"
            :reset-on-focus="true"
        />

        <div class="self-end">
            <button
                type="submit"
                class="inline-flex w-full items-center justify-center rounded-xl bg-zinc-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-700 md:min-w-[10rem] md:w-auto dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-300"
            >
                {{ __('Add') }}
            </button>
        </div>
    </form>

    <div class="mt-5 max-h-64 overflow-y-auto pr-3 [scrollbar-gutter:stable] md:max-h-72 md:pr-4 lg:max-h-[22rem] xl:max-h-[25rem] xl:pr-5" aria-label="{{ __('Observed currencies list') }}">
        <div class="space-y-3">
        @forelse ($watchedCurrencies as $watchedCurrency)
            <article class="rounded-2xl border border-zinc-200 bg-white/80 p-4 dark:border-zinc-700 dark:bg-zinc-900/80">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold tracking-wide text-zinc-900 dark:text-zinc-50">
                            {{ $watchedCurrency['code'] }} -
                            {{ $watchedCurrency['currency'] ?? __('This currency is not available in the latest NBP tables.') }}
                        </p>

                        @if ($watchedCurrency['rate'] !== null)
                            <p class="mt-2 text-lg font-semibold tracking-tight text-zinc-900 dark:text-zinc-50">
                                {{ number_format($watchedCurrency['rate'], 4, ',', ' ') }} PLN
                            </p>
                        @else
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ __('Current rate unavailable') }}
                            </p>
                        @endif

                    </div>

                    <form method="POST" action="{{ route('watched-currencies.destroy', $watchedCurrency['id']) }}" class="w-full shrink-0 sm:w-auto">
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            aria-label="{{ __('Remove :code from observed currencies', ['code' => $watchedCurrency['code']]) }}"
                            class="inline-flex w-full items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white sm:w-auto dark:border-red-900/70 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/70 dark:focus-visible:ring-red-800 dark:focus-visible:ring-offset-zinc-900"
                        >
                            {{ __('Remove') }}
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50/80 p-4 dark:border-zinc-700 dark:bg-zinc-950/70">
                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50">
                    {{ __('No watched currencies yet.') }}
                </p>
                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                    {{ __('Choose a currency above to start tracking its latest NBP rate.') }}
                </p>
            </div>
        @endforelse
        </div>
    </div>
</section>
