@props([
    'id',
    'name',
    'label',
    'currencies',
    'selectedCode' => '',
    'query' => '',
    'optionPrefix' => 'currency-option',
    'errorKey' => null,
    'placeholder' => null,
    'resetOnFocus' => false,
])

@php
    $errorKey = $errorKey ?? $name;
    $errorId = $id.'_error';
    $listboxId = $id.'_listbox';
@endphp

<div
    class="relative"
    x-on:click.outside="closeList()"
    x-data="{
        open: false,
        query: @js($query),
        selectedCode: @js($selectedCode),
        activeIndex: -1,
        resetOnFocus: @js($resetOnFocus),
        optionPrefix: @js($optionPrefix),
        currencies: @js(collect($currencies)->values()->all()),

        get filteredCurrencies() {
            let search = this.query.toLowerCase().trim();

            if (search === '') {
                return this.currencies;
            }

            return this.currencies.filter(currency =>
                currency.code.toLowerCase().includes(search) ||
                currency.currency.toLowerCase().includes(search)
            );
        },

        selectCurrency(currency) {
            this.selectedCode = currency.code;
            this.query = currency.code + ' - ' + currency.currency;
            this.closeList();
        },

        optionId(currency) {
            return this.optionPrefix + '-' + currency.code.toLowerCase();
        },

        openList(resetQuery = false) {
            if (resetQuery) {
                this.query = '';
            }

            this.open = true;
            this.activeIndex = this.filteredCurrencies.length > 0 ? 0 : -1;
        },

        closeList() {
            this.open = false;
            this.activeIndex = -1;
        },

        highlightNext() {
            if (! this.open) {
                this.openList();

                return;
            }

            if (this.filteredCurrencies.length === 0) {
                return;
            }

            this.activeIndex = (this.activeIndex + 1) % this.filteredCurrencies.length;
            this.scrollActiveOptionIntoView();
        },

        highlightPrevious() {
            if (! this.open) {
                this.openList();

                return;
            }

            if (this.filteredCurrencies.length === 0) {
                return;
            }

            this.activeIndex = this.activeIndex <= 0
                ? this.filteredCurrencies.length - 1
                : this.activeIndex - 1;

            this.scrollActiveOptionIntoView();
        },

        selectHighlighted() {
            if (! this.open) {
                this.openList();

                return;
            }

            let currency = this.filteredCurrencies[this.activeIndex];

            if (currency) {
                this.selectCurrency(currency);
            }
        },

        scrollActiveOptionIntoView() {
            this.$nextTick(() => {
                let currency = this.filteredCurrencies[this.activeIndex];

                if (! currency) {
                    return;
                }

                document.getElementById(this.optionId(currency))?.scrollIntoView({ block: 'nearest' });
            });
        }
    }"
>
    <label for="{{ $id }}" class="mb-2 block text-sm font-medium text-zinc-900 dark:text-zinc-50">
        {{ $label }}
    </label>

    <input type="hidden" name="{{ $name }}" :value="selectedCode">

    <input
        id="{{ $id }}"
        type="text"
        autocomplete="off"
        autocapitalize="off"
        spellcheck="false"
        role="combobox"
        aria-autocomplete="list"
        aria-controls="{{ $listboxId }}"
        x-bind:aria-expanded="open.toString()"
        x-bind:aria-activedescendant="open && activeIndex >= 0 && filteredCurrencies[activeIndex] ? optionId(filteredCurrencies[activeIndex]) : null"
        x-model="query"
        x-on:focus="openList(resetOnFocus)"
        x-on:input="openList()"
        x-on:click="openList(true)"
        x-on:keydown.arrow-down.prevent="highlightNext()"
        x-on:keydown.arrow-up.prevent="highlightPrevious()"
        x-on:keydown.enter.prevent="selectHighlighted()"
        x-on:keydown.escape.prevent="closeList()"
        x-on:keydown.tab="closeList()"
        placeholder="{{ $placeholder ?? __('Search currency') }}"
        @error($errorKey) aria-invalid="true" aria-describedby="{{ $errorId }}" @enderror
        class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 text-sm text-zinc-900 outline-none transition focus:border-zinc-500 focus:ring-2 focus:ring-zinc-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:border-zinc-500 dark:focus:ring-zinc-700"
    >

    <div
        id="{{ $listboxId }}"
        x-show="open"
        role="listbox"
        class="absolute z-50 mt-2 max-h-64 w-full overflow-y-auto rounded-xl border border-zinc-300 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-950"
    >
        <template x-for="(currency, index) in filteredCurrencies" :key="currency.code">
            <button
                x-bind:id="optionId(currency)"
                type="button"
                role="option"
                x-bind:aria-selected="selectedCode === currency.code"
                x-on:click="selectCurrency(currency)"
                x-on:mousemove="activeIndex = index"
                x-bind:class="activeIndex === index
                    ? 'bg-zinc-100 dark:bg-zinc-800'
                    : ''"
                class="block w-full px-3 py-2 text-left text-sm text-zinc-900 hover:bg-zinc-100 dark:text-zinc-100 dark:hover:bg-zinc-800"
            >
                <span x-text="currency.code + ' - ' + currency.currency"></span>
            </button>
        </template>
    </div>

    @error($errorKey)
        <p id="{{ $errorId }}" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
