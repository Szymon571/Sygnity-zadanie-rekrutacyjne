@props([
    'goldPrices',
])

@php
    $goldPriceValues = $goldPrices->pluck('cena');
    $latestGoldPrice = $goldPriceValues->last();
    $goldPriceChange = $goldPriceValues->count() > 1
        ? $goldPriceValues->last() - $goldPriceValues->slice(-2, 1)->first()
        : null;
@endphp

<div class="rounded-2xl border border-zinc-200 bg-zinc-50/80 p-5 dark:border-zinc-700 dark:bg-zinc-950/70">
    <h2 class="text-xs font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">
        {{ __('Latest gold price') }}
    </h2>
    <p class="mt-3 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-50">
        {{ $latestGoldPrice !== null ? number_format($latestGoldPrice, 2, ',', ' ') : '—' }}
    </p>
    <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
        @if ($goldPriceChange !== null)
            {{ __('Compared with previous quote: :change PLN', ['change' => number_format($goldPriceChange, 2, ',', ' ')]) }}
        @else
            {{ __('Not enough data to calculate the change yet.') }}
        @endif
    </p>
</div>
