@props([
    'goldPrices',
])

@php
    $orderedGoldPrices = $goldPrices->reverse()->values();
    $goldPriceDates = $goldPrices->pluck('data');
    $goldPriceValues = $goldPrices->pluck('cena');
@endphp

<section
    class="flex min-w-0 flex-col rounded-2xl border border-neutral-200 bg-white p-5 shadow-xs min-h-[28rem] md:min-h-[31rem] lg:min-h-[35rem] xl:min-h-[37.5rem] dark:border-neutral-700 dark:bg-neutral-900"
    x-data="{
        view: 'chart',
        storageKey: 'gold-prices-view',
        chart: null,
        goldLabels: @js($goldPriceDates->all()),
        goldValues: @js($goldPriceValues->all()),
        init() {
            try {
                let savedView = window.localStorage.getItem(this.storageKey);

                if (savedView === 'chart' || savedView === 'table') {
                    this.view = savedView;
                }
            } catch (error) {
            }

            if (this.view === 'chart') {
                this.syncChart();
            }
        },
        syncChart() {
            if (this.goldLabels.length === 0 || !window.createGoldPricesChart) {
                return;
            }

            this.$nextTick(() => {
                window.requestAnimationFrame(() => {
                    window.requestAnimationFrame(() => {
                        if (this.chart === null) {
                            this.chart = window.createGoldPricesChart(this.$refs.goldChartCanvas, this.goldLabels, this.goldValues);
                        }

                        if (this.chart) {
                            this.chart.resize();
                            this.chart.update('none');
                        }
                    });
                });
            });
        },
        showChart() {
            this.view = 'chart';

            try {
                window.localStorage.setItem(this.storageKey, 'chart');
            } catch (error) {
            }

            this.syncChart();
        },
        showTable() {
            this.view = 'table';

            try {
                window.localStorage.setItem(this.storageKey, 'table');
            } catch (error) {
            }
        }
    }"
    x-init="init()"
>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <h2 class="text-sm font-medium text-zinc-900 dark:text-zinc-50">{{ __('Gold prices') }}</h2>
            <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                {{ __('The latest :count NBP gold quotes.', ['count' => $goldPrices->count()]) }}
            </p>
        </div>
        <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto sm:justify-end">
            @if ($goldPriceDates->isNotEmpty())
                <span class="shrink-0 whitespace-nowrap rounded-full border border-zinc-200 px-2.5 py-1 text-xs font-medium text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                    {{ $goldPriceDates->first() }} - {{ $goldPriceDates->last() }}
                </span>
            @endif

            <div class="inline-flex shrink-0 rounded-full border border-zinc-200 bg-zinc-50 p-1 dark:border-zinc-700 dark:bg-zinc-950/70" role="group" aria-label="{{ __('Gold prices view switcher') }}">
                <button
                    type="button"
                    x-on:click="showChart()"
                    x-bind:aria-pressed="view === 'chart'"
                    x-bind:class="view === 'chart'
                        ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900'
                        : 'text-zinc-600 dark:text-zinc-300'"
                    class="rounded-full px-3 py-1.5 text-xs font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-zinc-600 dark:focus-visible:ring-offset-zinc-900"
                >
                    {{ __('Chart') }}
                </button>
                <button
                    type="button"
                    x-on:click="showTable()"
                    x-bind:aria-pressed="view === 'table'"
                    x-bind:class="view === 'table'
                        ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900'
                        : 'text-zinc-600 dark:text-zinc-300'"
                    class="rounded-full px-3 py-1.5 text-xs font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-zinc-600 dark:focus-visible:ring-offset-zinc-900"
                >
                    {{ __('Table') }}
                </button>
            </div>
        </div>
    </div>

    <div class="mt-5 min-h-0 flex-1 overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-50/50 dark:border-zinc-700 dark:bg-zinc-950/40">
        <div
            x-show="view === 'chart'"
            style="display: none;"
            class="h-full"
        >
            @if ($goldPrices->isNotEmpty())
                <div class="flex h-full flex-col">
                    <div aria-hidden="true" class="grid grid-cols-[minmax(0,1fr)_auto] bg-zinc-50 px-4 py-3 text-xs font-semibold uppercase tracking-[0.18em] opacity-0 dark:bg-zinc-950/70">
                        <span>{{ __('Date') }}</span>
                        <span>{{ __('Price (PLN)') }}</span>
                    </div>

                    <div class="relative mx-4 mb-4 mt-3 h-[20rem] overflow-hidden md:h-[23rem] lg:h-[25rem] xl:h-[27rem]">
                        <canvas x-ref="goldChartCanvas" class="block h-full w-full" role="img" aria-label="{{ __('Gold price chart') }}"></canvas>
                    </div>
                </div>
            @else
                <div class="flex h-full items-center justify-center text-sm text-zinc-600 dark:text-zinc-300">
                    {{ __('No gold prices are available right now.') }}
                </div>
            @endif
        </div>

        <div
            x-show="view === 'table'"
            style="display: none;"
            class="h-full"
        >
            <div class="flex h-full flex-col">
                <div class="grid grid-cols-[minmax(0,1fr)_auto] bg-zinc-50 px-4 py-3 text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:bg-zinc-950/70 dark:text-zinc-400">
                    <span>{{ __('Date') }}</span>
                    <span>{{ __('Price (PLN)') }}</span>
                </div>

                <div class="min-h-0 divide-y divide-zinc-200 overflow-y-auto [scrollbar-gutter:stable] dark:divide-zinc-700">
                    @forelse ($orderedGoldPrices as $goldPrice)
                        <div class="grid grid-cols-[minmax(0,1fr)_auto] px-4 py-3 text-sm text-zinc-700 dark:text-zinc-200">
                            <span>{{ $goldPrice['data'] }}</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-50">
                                {{ number_format($goldPrice['cena'], 2, ',', ' ') }}
                            </span>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-sm text-zinc-600 dark:text-zinc-300">
                            {{ __('No gold prices are available right now.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
