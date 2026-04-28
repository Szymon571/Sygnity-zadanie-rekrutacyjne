@php
    $toastVariant = session('status_variant', 'added');
    $toastBadgeClasses = $toastVariant === 'removed'
        ? 'border border-red-800/80 bg-red-950/70 text-red-300'
        : 'border border-emerald-800/80 bg-emerald-950/70 text-emerald-300';
    $toastBorderClasses = $toastVariant === 'removed'
        ? 'border-red-900/70'
        : 'border-emerald-900/70';
@endphp

@if (session('status'))
    <div
        x-data="{ open: true }"
        x-init="window.setTimeout(() => open = false, 4500)"
        x-show="open"
        x-transition.opacity.duration.200ms
        x-transition.scale.origin.bottom.right.duration.200ms
        class="fixed inset-x-4 bottom-4 z-50 sm:inset-x-auto sm:right-6 sm:bottom-6 sm:w-full sm:max-w-sm"
    >
        <div
            role="status"
            aria-live="polite"
            class="{{ $toastBorderClasses }} rounded-2xl border bg-zinc-950/95 p-4 text-zinc-50 shadow-xl shadow-black/30 backdrop-blur"
        >
            <div class="flex items-start gap-3">
                <span class="{{ $toastBadgeClasses }} inline-flex rounded-full px-2.5 py-1 text-xs font-semibold tracking-wide">
                    {{ $toastVariant === 'removed' ? __('Removed') : __('Added') }}
                </span>

                <p class="min-w-0 flex-1 text-sm font-medium leading-6 text-zinc-50">
                    {{ session('status') }}
                </p>

                <button
                    type="button"
                    x-on:click="open = false"
                    aria-label="{{ __('Close notification') }}"
                    class="inline-flex size-8 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-800 hover:text-zinc-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-300 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-950"
                >
                    <span aria-hidden="true" class="text-lg leading-none">&times;</span>
                </button>
            </div>
        </div>
    </div>
@endif
