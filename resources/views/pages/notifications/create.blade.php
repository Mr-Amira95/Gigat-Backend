@extends('layouts.master')
@section('title', __('notifications'))
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header (same style as create category) -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('send_notifications') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('notifications.index') }}">
                            {{ __('notifications') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('create') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('send_notifications') }}</h5>
                        </div>

                        <div class="box-body">
                            <form action="{{ route('notifications.send') }}" method="POST">
                                @csrf

                                <div class="grid grid-cols-12 gap-4">
                                    {{-- Audience --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('target_audience') }}</label>
                                        <select name="audience"
                                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary {{ $errors->has('audience') ? 'border-danger' : 'border-gray-300' }}"
                                            required>
                                            <option value="all" {{ old('audience') === 'all' ? 'selected' : '' }}>
                                                {{ __('all') }}</option>
                                            <option value="freelancer"
                                                {{ old('audience') === 'freelancer' ? 'selected' : '' }}>
                                                {{ __('freelancer') }}</option>
                                            <option value="client" {{ old('audience') === 'client' ? 'selected' : '' }}>
                                                {{ __('client') }}</option>
                                        </select>
                                        @error('audience')
                                            <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Platform --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('platform') }}</label>
                                        <select name="platform"
                                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary {{ $errors->has('platform') ? 'border-danger' : 'border-gray-300' }}"
                                            required>
                                            <option value="all" {{ old('platform') === 'all' ? 'selected' : '' }}>
                                                {{ __('all') }}</option>
                                            <option value="ios" {{ old('platform') === 'ios' ? 'selected' : '' }}>
                                                {{ __('ios') }}</option>
                                            <option value="android" {{ old('platform') === 'android' ? 'selected' : '' }}>
                                                {{ __('android') }}</option>
                                            <option value="web" {{ old('platform') === 'web' ? 'selected' : '' }}>
                                                {{ __('web') }}</option>
                                        </select>
                                        @error('platform')
                                            <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Title EN --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('title_en') }}</label>
                                        <input type="text" name="title_en" value="{{ old('title_en') }}"
                                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary {{ $errors->has('title_en') ? 'border-danger' : 'border-gray-300' }}"
                                            required>
                                        @error('title_en')
                                            <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Title AR --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('title_ar') }}</label>
                                        <input type="text" name="title_ar" value="{{ old('title_ar') }}"
                                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary {{ $errors->has('title_ar') ? 'border-danger' : 'border-gray-300' }}"
                                            required>
                                        @error('title_ar')
                                            <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Body EN --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('body_en') }}</label>
                                        <textarea name="body_en" rows="4"
                                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary {{ $errors->has('body_en') ? 'border-danger' : 'border-gray-300' }}"
                                            required>{{ old('body_en') }}</textarea>
                                        @error('body_en')
                                            <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Body AR --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('body_ar') }}</label>
                                        <textarea name="body_ar" rows="4"
                                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary {{ $errors->has('body_ar') ? 'border-danger' : 'border-gray-300' }}"
                                            required>{{ old('body_ar') }}</textarea>
                                        @error('body_ar')
                                            <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Type --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('type_optional') }}</label>
                                        <select name="notif_type" id="notif_type"
                                            class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary {{ $errors->has('notif_type') ? 'border-danger' : 'border-gray-300' }}">
                                            <option value="">{{ __('select_type') }}</option>
                                            <option value="categories"
                                                {{ old('notif_type') === 'categories' ? 'selected' : '' }}>
                                                {{ __('categories') }}</option>
                                            <option value="services"
                                                {{ old('notif_type') === 'services' ? 'selected' : '' }}>
                                                {{ __('services') }}</option>
                                            <option value="portfolio"
                                                {{ old('notif_type') === 'portfolio' ? 'selected' : '' }}>
                                                {{ __('portfolio') }}</option>
                                        </select>
                                        {{-- <small class="text-gray-500">{{ __('Required if a type is selected') }}</small> --}}
                                        @error('notif_type')
                                            <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Item (filtered by type) --}}
                                    <div class="col-span-12 md:col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('select_item') }}</label>
                                        <select name="notif_id" id="notif_id"
                                            class="js-example-basic-multiple mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary {{ $errors->has('notif_id') ? 'border-danger' : 'border-gray-300' }}">
                                            <option value="">{{ __('select_item') }}</option>

                                            {{-- Categories --}}
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}" data-type="categories"
                                                    {{ old('notif_type') === 'categories' && old('notif_id') == $cat->id ? 'selected' : '' }}>
                                                    {{ optional($cat->translation)->title ??
                                                        (method_exists($cat, 'translateOrDefault')
                                                            ? optional($cat->translateOrDefault())->title
                                                            : $cat->title ?? ($cat->name ?? 'Category #' . $cat->id)) }}
                                                </option>
                                            @endforeach

                                            {{-- Services --}}
                                            @foreach ($services as $srv)
                                                <option value="{{ $srv->id }}" data-type="services"
                                                    {{ old('notif_type') === 'services' && old('notif_id') == $srv->id ? 'selected' : '' }}>
                                                    {{ optional($srv->translation)->title ??
                                                        (method_exists($srv, 'translateOrDefault')
                                                            ? optional($srv->translateOrDefault())->title
                                                            : $srv->title ?? 'Service #' . $srv->id) }}
                                                </option>
                                            @endforeach

                                            {{-- Portfolios --}}
                                            @foreach ($portfolios as $port)
                                                <option value="{{ $port->id }}" data-type="portfolio"
                                                    {{ old('notif_type') === 'portfolio' && old('notif_id') == $port->id ? 'selected' : '' }}>
                                                    {{ optional($port->translation)->title ??
                                                        (method_exists($port, 'translateOrDefault')
                                                            ? optional($port->translateOrDefault())->title
                                                            : $port->title ?? 'Portfolio #' . $port->id) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small id="type-hint" class="text-gray-500 hidden">
                                            {{ __('Required if a type is selected') }}
                                        </small>

                                        @error('notif_id')
                                            <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-center">
                                    <button type="submit"
                                        class="px-6 py-2 text-white bg-primary hover:bg-primary-700 rounded-md shadow-sm">
                                        <i class="las la-paper-plane"></i> {{ __('send_notification') }}
                                    </button>
                                </div>
                            </form>
                        </div> {{-- box-body --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        (function() {
            const typeSelect = document.getElementById('notif_type');
            const idSelect = document.getElementById('notif_id');
            const hint = document.getElementById('type-hint');

            // Store all options initially
            const allOptions = Array.from(idSelect.options);

            // Init Select2 (disabled at start)
            $(idSelect).select2({
                placeholder: "{{ __('select_item') }}",
                allowClear: true,
                width: '100%'
            }).prop('disabled', true);

            function filterOptions() {
                const t = typeSelect.value;

                // Reset current options
                $(idSelect).empty();

                if (t) {
                    // Filter only the ones matching the type
                    const filtered = allOptions.filter(opt => opt.dataset.type === t);

                    // Add a placeholder
                    $(idSelect).append(new Option("{{ __('select_item') }}", ""));

                    // Append filtered options
                    filtered.forEach(opt => {
                        $(idSelect).append(new Option(opt.text, opt.value));
                    });

                    $(idSelect).prop('disabled', false);
                    idSelect.setAttribute('required', 'required');
                    hint?.classList.remove('hidden');
                } else {
                    $(idSelect).prop('disabled', true);
                    idSelect.removeAttribute('required');
                    hint?.classList.add('hidden');
                }

                // Refresh Select2
                $(idSelect).val(null).trigger('change');
            }

            filterOptions();
            typeSelect.addEventListener('change', filterOptions);
        })();
    </script>
@endpush

{{-- @push('scripts')
    <script>
        (function() {
            const typeSelect = document.getElementById('notif_type');
            const idSelect = document.getElementById('notif_id');
            const hint = document.getElementById('type-hint');

            function filterOptions() {
                const t = typeSelect.value;

                Array.from(idSelect.options).forEach(opt => {
                    if (!opt.value) return;
                    opt.style.display = (opt.dataset.type === t) ? 'block' : 'none';
                });

                if (idSelect.selectedOptions[0]?.dataset.type !== t) idSelect.value = '';

                // required only when type is chosen
                if (t) {
                    idSelect.setAttribute('required', 'required');
                    hint?.classList.remove('hidden');
                } else {
                    idSelect.removeAttribute('required');
                    hint?.classList.add('hidden');
                }
            }

            filterOptions();
            typeSelect.addEventListener('change', filterOptions);
        })();
    </script>
@endpush --}}
