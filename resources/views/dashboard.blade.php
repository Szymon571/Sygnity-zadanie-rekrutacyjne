<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-3xl">
        <section class="overflow-hidden rounded-3xl border border-neutral-200 bg-white/70 p-6 shadow-xs backdrop-blur-sm dark:border-neutral-700 dark:bg-neutral-900/70">
            <div class="grid gap-6 lg:grid-cols-[1.4fr_0.9fr]">
                <div class="space-y-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">
                        {{ __('Dashboard') }}
                    </p>
                    <div class="space-y-3">
                        <h1 class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-50">
                            {{ __('Track currencies and gold in one dashboard.') }}
                        </h1>
                        <div class="grid max-w-2xl gap-3">
                            <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/70 px-4 py-3 text-sm leading-6 text-zinc-600 dark:border-zinc-700/80 dark:bg-zinc-900/50 dark:text-zinc-300">
                                {{ __('Monitor watched currencies, search historical NBP rates, and review the latest gold prices in one place.') }}
                            </div>
                            <div class="rounded-2xl border border-zinc-200/80 bg-zinc-50/70 px-4 py-3 text-sm leading-6 text-zinc-600 dark:border-zinc-700/80 dark:bg-zinc-900/50 dark:text-zinc-300">
                                {{ __('NBP publishes rates on business days. If a selected date has no quote, the app uses the nearest earlier available publication.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    <x-dashboard.rate-search-card
                        :available-currencies="$availableCurrencies"
                        :searched-currency="$searchedCurrency"
                        :searched-date="$searchedDate"
                        :searched-rate="$searchedRate"
                        :search-error="$searchError"
                    />

                    <x-dashboard.latest-gold-price-card :gold-prices="$goldPrices" />
                </div>
            </div>
        </section>

        <div class="grid items-start gap-4 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
            <x-dashboard.observed-currencies-card
                :watched-currencies="$watchedCurrencies"
                :available-currencies="$availableCurrencies"
            />

            <div class="grid min-w-0 gap-4">
                <x-dashboard.gold-prices-card :gold-prices="$goldPrices" />
            </div>
        </div>

        <x-dashboard.toast />
    </div>
</x-layouts::app>
