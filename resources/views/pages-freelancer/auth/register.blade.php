@extends('layouts.auth')
@section('title', __('register_freelancer'))

@push('styles')
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" /> --}}
@endpush

@section('content')
    <div class="w-full max-w-3xl relative z-10">
        {{-- Logo --}}
        <div class="text-center mb-4 fade-in">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg mb-4">
                <img src="{{ asset($logo ?? 'images/logo.png') }}" alt="Gigat" class="w-20 h-12 object-contain">
            </div>
        </div>

        {{-- Card --}}
        <div class="glass-effect rounded-2xl p-8 shadow-2xl fade-in" style="animation-delay:.2s;">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ __('register_freelancer') }}</h2>
                {{-- <p class="text-base text-gray-500">{{ __('welcome_back') }}</p> --}}
            </div>
            <form action="{{ route('freelancer.register.submit') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                {{-- Avatar --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-image mr-2 text-purple-600"></i>{{ __('avatar') }}
                    </label>
                    <input type="file" name="avatar" class="w-full border border-gray-300 rounded-lg p-2 bg-white">
                    @error('avatar')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Username --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-purple-600"></i>{{ __('username') }}
                    </label>
                    <input type="text" name="username" value="{{ session('google_name') ?? old('username') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('username') border-red-500 @enderror">
                    @error('username')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                @if (session('google_id'))
                    <input type="hidden" name="google_id" value="{{ session('google_id') ?? old('google_id') }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-purple-600"></i>{{ __('email') }}
                        </label>
                        <input type="email" name="email" value="{{ session('google_email') ?? old('email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" readonly>
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-purple-600"></i>{{ __('email') }}
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                {{-- Password / Confirm (skipped when Google) --}}
                @if (session('google_id'))
                    <input type="hidden" name="password" value="{{ session('password') }}">
                    <input type="hidden" name="password_confirmation" value="{{ session('password') }}">
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-purple-600"></i>{{ __('password') }}
                        </label>
                        <input type="password" name="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-check-double mr-2 text-purple-600"></i>{{ __('confirm_password') }}
                        </label>
                        <input type="password" name="password_confirmation"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus">
                    </div>
                @endif

                {{-- Prefix & Phone --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-flag mr-2 text-purple-600"></i>{{ __('prefix') }}
                        </label>
                        <select name="prefix" class="form-select js-select w-full">
                            @foreach ($countries as $country)
                                <option value="{{ $country->phone_code }}"
                                    {{ old('prefix') == $country->phone_code ? 'selected' : '' }}>
                                    {{ $country->phone_code }}
                                </option>
                            @endforeach
                        </select>
                        @error('prefix')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-purple-600"></i>{{ __('phone') }}
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-venus-mars mr-2 text-purple-600"></i>{{ __('gender') }}
                    </label>
                    <select name="gender" class="form-select js-select w-full">
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>{{ __('male') }}
                        </option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>{{ __('female') }}
                        </option>
                        <option value="prefer_not_say" {{ old('gender') === 'prefer_not_say' ? 'selected' : '' }}>{{ __('prefer_not_say') }}</option>

                    </select>
                </div>

                {{-- Profession --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-briefcase mr-2 text-purple-600"></i>{{ __('profession') }}
                    </label>
                    <select name="profession_id" class="form-select js-select w-full">
                        @foreach ($professions as $profession)
                            <option value="{{ $profession->id }}"
                                {{ old('profession_id') == $profession->id ? 'selected' : '' }}>
                                {{ $profession->translations->firstWhere('language', app()->getLocale())?->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Bio --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-2 text-purple-600"></i>{{ __('bio') }}
                    </label>
                    <textarea name="bio" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus @error('bio') border-red-500 @enderror">{{ old('bio') }}</textarea>
                    @error('bio')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Country --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-globe mr-2 text-purple-600"></i>{{ __('country') }}
                    </label>
                    <select name="country_id" class="form-select js-select w-full">
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Languages --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-language mr-2 text-purple-600"></i>{{ __('languages') }}
                    </label>
                    <select name="languages[]" class="form-select js-select-multiple w-full" multiple>
                        @foreach ($languages as $language)
                            <option value="{{ $language->id }}"
                                {{ collect(old('languages', []))->contains($language->id) ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'ar' ? $language->title_ar : $language->title_en }}
                            </option>
                        @endforeach
                    </select>
                    @error('languages')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Categories --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-2 text-purple-600"></i>{{ __('categories') }}
                    </label>
                    <select name="category_ids[]" class="form-select js-select-multiple w-full" multiple>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ collect(old('category_ids', []))->contains($category->id) ? 'selected' : '' }}>
                                {{ $category->translation->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_ids')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Certificates (file + description pairs) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-certificate mr-2 text-purple-600"></i>{{ __('certificates') }}
                    </label>
                    <div id="certificate-wrapper" class="space-y-2">
                        <div class="flex gap-2">
                            <input type="file" name="file[]"
                                class="flex-1 border border-gray-300 rounded-lg p-2 bg-white file-input">
                            <input type="text" name="description[]" placeholder="{{ __('certificate_description') }}"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus description-input">
                        </div>
                    </div>
                    <button type="button" id="add-certificate-btn"
                        class="text-purple-700 hover:text-purple-900 text-sm mt-2">
                        + {{ __('add_more_certificates') }}
                    </button>
                </div>

                {{-- Submit --}}
                <div>
                    <button type="submit"
                        class="w-full btn-primary text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-4 focus:ring-purple-200">
                        {{ __('register') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {!! JsValidator::formRequest('App\\Http\\Requests\\Admin\\FreelancerRequest') !!}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Certificates: require description only when file chosen + allow add more rows
        document.addEventListener('change', e => {
            if (!e.target.classList.contains('file-input')) return;
            const desc = e.target.closest('.flex').querySelector('.description-input');
            if (e.target.value) desc.setAttribute('required', 'required');
            else desc.removeAttribute('required');
        });

        document.getElementById('add-certificate-btn')?.addEventListener('click', () => {
            const html = `
        <div class="flex gap-2">
          <input type="file" name="file[]" class="flex-1 border border-gray-300 rounded-lg p-2 bg-white file-input">
          <input type="text" name="description[]" placeholder="{{ __('certificate_description') }}"
                 class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none input-focus description-input">
        </div>`;
            document.getElementById('certificate-wrapper').insertAdjacentHTML('beforeend', html);
        });

        // Select2 (search enabled)
        // (function initSelects() {
        //     if (!window.jQuery || !jQuery.fn.select2) return setTimeout(initSelects, 120);

        //     $('.js-select').select2({
        //         width: '100%',
        //         minimumResultsForSearch: 0, // always show search
        //         dropdownAutoWidth: true
        //     });

        //     $('.js-select-multiple').select2({
        //         width: '100%',
        //         minimumResultsForSearch: 0,
        //         dropdownAutoWidth: true,
        //         closeOnSelect: false,
        //         placeholder: "{{ __('select_options') }}"
        //     });
        // })();
    </script>

    <script>
        (function initRegisterSelects() {
            if (!window.jQuery || !jQuery.fn.select2) return setTimeout(initRegisterSelects, 100);

            // 1) Prefix (same behavior as login)
            const $prefix = $('#prefixRegister, [name="prefix"]'); // support either id or name
            if ($prefix.length) {
                $prefix.select2({
                    width: 'style', // keep the compact width like login
                    dropdownAutoWidth: true,
                    minimumResultsForSearch: 0, // ALWAYS show search
                    placeholder: "{{ __('select_prefix') }}",
                    allowClear: false,
                    dropdownParent: $(document.body)
                });
                // Match input look
                try {
                    const $sel = $prefix.data('select2').$container.find('.select2-selection--single');
                    $sel.css({
                        height: '48px',
                        borderRadius: '12px',
                        borderColor: '#D1D5DB',
                        display: 'flex',
                        alignItems: 'center',
                        padding: '0 .75rem',
                        backgroundColor: '#fff'
                    });
                } catch (e) {}
            }

            // 2) Single selects (gender, profession, country, etc.)
            $('.js-select').each(function() {
                const $el = $(this);
                $el.select2({
                    width: '100%', // full width for normal fields
                    dropdownAutoWidth: true,
                    minimumResultsForSearch: 0, // ALWAYS show search
                    placeholder: "{{ __('select_options') }}",
                    allowClear: false,
                    dropdownParent: $(document.body)
                });
                try {
                    const $sel = $el.data('select2').$container.find('.select2-selection--single');
                    $sel.css({
                        height: '48px',
                        borderRadius: '12px',
                        borderColor: '#D1D5DB',
                        display: 'flex',
                        alignItems: 'center',
                        padding: '0 .75rem',
                        backgroundColor: '#fff'
                    });
                } catch (e) {}
            });

            // 3) Multiple selects (languages, categories, etc.)
            $('.js-select-multiple').each(function() {
                const $el = $(this);
                $el.select2({
                    width: '100%',
                    dropdownAutoWidth: true,
                    minimumResultsForSearch: 0, // ALWAYS show search
                    closeOnSelect: false,
                    placeholder: "{{ __('select_options') }}",
                    allowClear: true,
                    dropdownParent: $(document.body)
                });
                try {
                    const $sel = $el.data('select2').$container.find('.select2-selection--multiple');
                    $sel.css({
                        minHeight: '48px',
                        borderRadius: '12px',
                        borderColor: '#D1D5DB',
                        padding: '4px 6px',
                        backgroundColor: '#fff'
                    });
                } catch (e) {}
            });
        })();
    </script>
@endpush
