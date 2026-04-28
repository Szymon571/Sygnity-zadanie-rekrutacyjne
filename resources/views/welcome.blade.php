@php
    $appName = config('app.name') !== 'Laravel'
        ? config('app.name')
        : __('Currency Watch');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => __('Welcome')])
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-800 dark:text-zinc-100">
        <div class="relative isolate overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(161,161,170,0.18),_transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(82,82,91,0.12),_transparent_38%)] dark:bg-[radial-gradient(circle_at_top_left,_rgba(244,244,245,0.12),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(113,113,122,0.14),_transparent_36%)]"></div>

            <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3 font-medium" wire:navigate>
                    <span class="flex size-10 items-center justify-center rounded-xl bg-zinc-900 text-white shadow-sm dark:bg-zinc-100 dark:text-zinc-900">
                        <x-app-logo-icon class="size-5" />
                    </span>
                    <span class="text-sm tracking-[0.18em] uppercase text-zinc-600 dark:text-zinc-300">{{ $appName }}</span>
                </a>

                <nav class="flex items-center gap-3">
                    <x-language-switcher />

                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="rounded-full border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 transition hover:border-zinc-400 hover:text-zinc-900 dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-zinc-500 dark:hover:text-white"
                            wire:navigate
                        >
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="rounded-full border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 transition hover:border-zinc-400 hover:text-zinc-900 dark:border-zinc-700 dark:text-zinc-200 dark:hover:border-zinc-500 dark:hover:text-white"
                            wire:navigate
                        >
                            {{ __('Log in') }}
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="rounded-full bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-300"
                                wire:navigate
                            >
                                {{ __('Create account') }}
                            </a>
                        @endif
                    @endauth
                </nav>
            </header>

            <main class="mx-auto grid w-full max-w-6xl gap-12 px-6 pb-16 pt-8 lg:grid-cols-[1.15fr_0.85fr] lg:px-8 lg:pb-24 lg:pt-16">
                <section class="space-y-8">
                    <div class="inline-flex items-center rounded-full border border-zinc-300 bg-white/80 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-zinc-600 shadow-xs backdrop-blur-sm dark:border-zinc-700 dark:bg-zinc-900/80 dark:text-zinc-300">
                        {{ __('Currency monitoring') }}
                    </div>

                    <div class="space-y-5">
                        <h1 class="max-w-3xl text-4xl font-semibold tracking-tight text-zinc-950 sm:text-5xl dark:text-zinc-50">
                            {{ __('Exchange rates and gold prices in one place.') }}
                        </h1>
                        <p class="max-w-2xl text-base leading-7 text-zinc-600 dark:text-zinc-300">
                            {{ __('Monitor watched currencies, search historical NBP rates, and review recent gold price movements in a clear dashboard built around NBP data.') }}
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <section class="rounded-2xl border border-zinc-200 bg-white/80 p-5 shadow-xs backdrop-blur-sm dark:border-zinc-700 dark:bg-zinc-900/80">
                            <p class="text-sm font-medium text-zinc-950 dark:text-zinc-50">{{ __('Watched currencies') }}</p>
                            <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                {{ __('Build a personal watchlist and keep current NBP rates close at hand.') }}
                            </p>
                        </section>

                        <section class="rounded-2xl border border-zinc-200 bg-white/80 p-5 shadow-xs backdrop-blur-sm dark:border-zinc-700 dark:bg-zinc-900/80">
                            <p class="text-sm font-medium text-zinc-950 dark:text-zinc-50">{{ __('Gold prices') }}</p>
                            <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                {{ __('Review the latest gold prices as a chart or a detailed table.') }}
                            </p>
                        </section>

                        <section class="rounded-2xl border border-zinc-200 bg-white/80 p-5 shadow-xs backdrop-blur-sm dark:border-zinc-700 dark:bg-zinc-900/80">
                            <p class="text-sm font-medium text-zinc-950 dark:text-zinc-50">{{ __('Historical lookup') }}</p>
                            <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                {{ __('Check the published rate for any supported currency on a selected date.') }}
                            </p>
                        </section>
                    </div>
                </section>

                <aside class="rounded-[2rem] border border-zinc-200 bg-white/85 p-6 shadow-sm backdrop-blur-sm dark:border-zinc-700 dark:bg-zinc-900/85">
                    <div class="rounded-[1.5rem] border border-dashed border-zinc-300 bg-zinc-50/80 p-6 dark:border-zinc-700 dark:bg-zinc-950/70">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">
                            {{ __('What you can do') }}
                        </p>
                        <div class="mt-4 space-y-4">
                            <div>
                                <p class="text-sm font-medium text-zinc-950 dark:text-zinc-50">{{ __('Authentication') }}</p>
                                <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                    {{ __('Create an account, sign in, update your password, and manage your profile securely.') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-zinc-950 dark:text-zinc-50">{{ __('Current market view') }}</p>
                                <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                    {{ __('Follow observed currencies and gold prices based on current NBP publications.') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-zinc-950 dark:text-zinc-50">{{ __('Historical search') }}</p>
                                <p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                                    {{ __('Browse historical exchange rates and use the nearest earlier publication when a quote is unavailable.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </aside>
            </main>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
