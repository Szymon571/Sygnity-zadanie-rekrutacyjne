@php
    $currentLocale = app()->currentLocale();

    $buttonBaseClasses = 'inline-flex min-w-11 items-center justify-center rounded-full px-3 py-1.5 text-xs font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-zinc-500 dark:focus-visible:ring-offset-zinc-900';
    $activeButtonClasses = 'bg-zinc-900 text-white shadow-sm dark:bg-zinc-100 dark:text-zinc-900';
    $inactiveButtonClasses = 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-50';
@endphp

<div {{ $attributes->class('inline-flex items-center rounded-full border border-zinc-200 bg-white/85 p-1 shadow-xs backdrop-blur-sm dark:border-zinc-700 dark:bg-zinc-900/85') }} role="group" aria-label="{{ __('Language switcher') }}">
    <form method="POST" action="{{ route('locale.update', 'en') }}">
        @csrf

        <button
            type="submit"
            aria-label="{{ __('Switch to English') }}"
            aria-pressed="{{ $currentLocale === 'en' ? 'true' : 'false' }}"
            class="{{ $buttonBaseClasses }} {{ $currentLocale === 'en' ? $activeButtonClasses : $inactiveButtonClasses }}"
        >
            EN
        </button>
    </form>

    <form method="POST" action="{{ route('locale.update', 'pl') }}">
        @csrf

        <button
            type="submit"
            aria-label="{{ __('Switch to Polish') }}"
            aria-pressed="{{ $currentLocale === 'pl' ? 'true' : 'false' }}"
            class="{{ $buttonBaseClasses }} {{ $currentLocale === 'pl' ? $activeButtonClasses : $inactiveButtonClasses }}"
        >
            PL
        </button>
    </form>
</div>
