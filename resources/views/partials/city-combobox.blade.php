@php
    $selectedValue = (string) old($name, $selected ?? '');
    $selectedCity = $cities->first(fn ($city) => (string) $city->id === $selectedValue);
    $inputId = $id ?? str_replace(['[', ']', '_'], '-', $name);
@endphp

<div class="relative" data-city-combobox>
    <input type="hidden" name="{{ $name }}" value="{{ $selectedValue }}" data-city-id>
    <button
        id="{{ $inputId }}"
        type="button"
        class="{{ $inputClass }} flex items-center justify-between gap-3 text-left"
        aria-haspopup="listbox"
        aria-expanded="false"
        data-city-trigger
    >
        <span class="{{ $selectedCity ? '' : 'text-slate-400' }}" data-city-label>{{ $selectedCity?->name ?? ($placeholder ?? 'Select city') }}</span>
        <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
        </svg>
    </button>

    <div class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-50 hidden overflow-hidden rounded-[1.25rem] border border-slate-200 bg-white shadow-[0_24px_70px_-28px_rgba(15,23,42,0.45)]" data-city-panel>
        <div class="border-b border-slate-100 p-2">
            <input
                type="search"
                class="w-full rounded-xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50"
                placeholder="Search city"
                autocomplete="off"
                data-city-search
            >
        </div>
        <div class="max-h-64 overflow-y-auto p-2" role="listbox" data-city-options>
            @foreach ($cities as $city)
                <button
                    type="button"
                    class="flex w-full items-center rounded-xl px-4 py-2.5 text-left text-sm font-semibold text-slate-700 transition hover:bg-brand-50 hover:text-brand-700 data-[selected=true]:bg-brand-50 data-[selected=true]:text-brand-700"
                    data-city-option
                    data-city-id="{{ $city->id }}"
                    data-city-name="{{ $city->name }}"
                    data-selected="{{ (string) $city->id === $selectedValue ? 'true' : 'false' }}"
                    role="option"
                    aria-selected="{{ (string) $city->id === $selectedValue ? 'true' : 'false' }}"
                >
                    {{ $city->name }}
                </button>
            @endforeach
        </div>
        <p class="hidden px-4 py-3 text-sm font-semibold text-slate-400" data-city-empty>No city found</p>
    </div>
</div>
