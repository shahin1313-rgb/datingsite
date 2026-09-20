@props([
    'id',
    'name',
    'options' => [],
    'value' => '',
    'placeholder' => 'انتخاب کنید',
    'required' => false,
])

<div
    data-custom-select
    class="relative min-w-0 w-full"
    x-data="{
        open: false,
        value: @js((string) $value),
        options: @js(
            collect($options)
                ->map(fn ($label, $optionValue) => [
                    'value' => (string) $optionValue,
                    'label' => (string) $label,
                ])
                ->values()
        ),
        placeholder: @js($placeholder),
        get selectedLabel() {
            return this.options.find(
                option => option.value === this.value
            )?.label || this.placeholder;
        },
        choose(option) {
            this.value = option.value;
            this.open = false;

            this.$nextTick(() => {
                this.$refs.nativeSelect.dispatchEvent(
                    new Event('change', { bubbles: true })
                );
                this.$refs.trigger.focus();
            });
        }
    }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
>
    <select
        x-ref="nativeSelect"
        x-model="value"
        id="{{ $id }}"
        name="{{ $name }}"
        @if($required) required @endif
        tabindex="-1"
        aria-hidden="true"
        class="sr-only"
    >
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $optionValue => $label)
            <option value="{{ $optionValue }}">{{ $label }}</option>
        @endforeach
    </select>

    <button
        x-ref="trigger"
        data-select-trigger
        type="button"
        class="flex w-full min-w-0 items-center justify-between gap-3 rounded-2xl border border-gray-200 bg-gray-50 p-3 text-right text-gray-800 outline-none transition-all focus:border-pink-500 focus:ring focus:ring-pink-200 dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-200 dark:focus:ring-pink-900/30"
        :aria-expanded="open"
        aria-haspopup="listbox"
        @click="open = !open"
    >
        <span class="min-w-0 flex-1 truncate" x-text="selectedLabel"></span>
        <svg
            class="h-4 w-4 shrink-0 transition-transform"
            :class="{ 'rotate-180': open }"
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
        >
            <path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition.origin.top
        role="listbox"
        class="absolute inset-x-0 top-full z-50 mt-1 max-h-52 w-full min-w-0 overflow-y-auto overflow-x-hidden rounded-2xl border border-gray-200 bg-white p-1 shadow-xl dark:border-slate-700 dark:bg-slate-800"
    >
        <template x-for="option in options" :key="option.value">
            <button
                type="button"
                role="option"
                class="flex w-full min-w-0 items-center rounded-xl px-3 py-2.5 text-right text-sm text-gray-700 transition hover:bg-pink-50 hover:text-pink-700 focus:bg-pink-50 focus:text-pink-700 focus:outline-none dark:text-slate-200 dark:hover:bg-pink-950/40 dark:hover:text-pink-300"
                :class="{ 'bg-pink-50 text-pink-700 dark:bg-pink-950/40 dark:text-pink-300': value === option.value }"
                :aria-selected="value === option.value"
                @click="choose(option)"
            >
                <span class="min-w-0 flex-1 break-words" x-text="option.label"></span>
                <span x-show="value === option.value" class="mr-2 shrink-0 text-pink-500">✓</span>
            </button>
        </template>
    </div>
</div>
