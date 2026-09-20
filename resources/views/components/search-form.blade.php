<form action="{{ route('search') }}" method="GET" class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4" dir="{{ app()->isLocale('fa') ? 'rtl' : 'ltr' }}">
    <div>
        <label for="city" class="mb-2 block text-sm font-bold text-zinc-200">{{ __('ui.city') }}</label>
        <input id="city" type="text" name="city" value="{{ request('city') }}" placeholder="{{ __('ui.city_placeholder') }}" class="vlora-field" aria-invalid="{{ $errors->has('city') ? 'true' : 'false' }}">
        @error('city')<p class="mt-2 text-xs text-red-400">{{ $message }}</p>@enderror
    </div>

    <fieldset>
        <legend class="mb-2 text-sm font-bold text-zinc-200">{{ __('ui.age_range') }}</legend>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label for="min_age" class="sr-only">{{ __('ui.minimum_age') }}</label>
                <input id="min_age" type="number" name="min_age" min="18" max="100" value="{{ request('min_age') }}" placeholder="{{ __('ui.from') }}" class="vlora-field" aria-invalid="{{ $errors->has('min_age') ? 'true' : 'false' }}">
            </div>
            <div>
                <label for="max_age" class="sr-only">{{ __('ui.maximum_age') }}</label>
                <input id="max_age" type="number" name="max_age" min="18" max="100" value="{{ request('max_age') }}" placeholder="{{ __('ui.to') }}" class="vlora-field" aria-invalid="{{ $errors->has('max_age') ? 'true' : 'false' }}">
            </div>
        </div>
    </fieldset>

    <div>
        <label for="marital_status" class="mb-2 block text-sm font-bold text-zinc-200">{{ __('ui.marital_status') }}</label>
        <select id="marital_status" name="marital_status" class="vlora-field">
            <option value="">{{ __('ui.all') }}</option>
            <option value="single" @selected(request('marital_status') === 'single')>{{ __('ui.single') }}</option>
            <option value="married" @selected(request('marital_status') === 'married')>{{ __('ui.married') }}</option>
            <option value="divorced" @selected(request('marital_status') === 'divorced')>{{ __('ui.divorced') }}</option>
            <option value="widowed" @selected(request('marital_status') === 'widowed')>{{ __('ui.widowed') }}</option>
        </select>
    </div>

    <div>
        <label for="interested_in" class="mb-2 block text-sm font-bold text-zinc-200">{{ __('ui.interests') }}</label>
        <select id="interested_in" name="interested_in" class="vlora-field">
            <option value="">{{ __('ui.any_interest') }}</option>
            <option value="sport" @selected(request('interested_in') === 'sport')>{{ __('ui.sport') }}</option>
            <option value="travel" @selected(request('interested_in') === 'travel')>{{ __('ui.travel') }}</option>
            <option value="books" @selected(request('interested_in') === 'books')>{{ __('ui.books') }}</option>
            <option value="party" @selected(request('interested_in') === 'party')>{{ __('ui.party') }}</option>
        </select>
    </div>

    <div class="flex flex-wrap items-center gap-5 md:col-span-2 lg:col-span-3">
        <label class="inline-flex min-h-11 cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 text-sm font-bold text-zinc-200">
            <input type="checkbox" name="has_photo" value="1" @checked(request('has_photo')) class="h-5 w-5 rounded border-zinc-600 bg-zinc-800 text-rose-500 focus:ring-rose-500">
            {{ __('ui.photo_only') }}
        </label>
        <label class="inline-flex min-h-11 cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 text-sm font-bold text-zinc-200">
            <input type="checkbox" name="is_active" value="1" @checked(request('is_active')) class="h-5 w-5 rounded border-zinc-600 bg-zinc-800 text-rose-500 focus:ring-rose-500">
            {{ __('ui.online_only') }}
        </label>
    </div>

    <button type="submit" class="vlora-btn-primary w-full lg:self-end">
        <i class="fas fa-search" aria-hidden="true"></i>
        {{ __('ui.advanced_search') }}
    </button>
</form>
