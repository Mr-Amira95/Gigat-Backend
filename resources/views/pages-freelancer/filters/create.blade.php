@extends('layouts.master')
@section('title', __('create_filter'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('create_filter') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary hover:text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold text-primary">{{ __('create_filter') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('create_filter') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('filters.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('category') }}</label>
                                        <select name="category_id[]"
                                            class="js-example-basic-multiple mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            multiple>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->translation->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('type') }}</label>
                                        <select name="type" id="type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <option value="">{{ __('select_type') }}</option>
                                            @foreach (App\Enums\FilterTypeEnum::cases() as $type)
                                                <option value="{{ $type->value }}"
                                                    {{ old('type') == $type->value ? 'selected' : '' }}>
                                                    {{ $type->label() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('title_en') }}</label>
                                        <input type="text" name="translations[en][title]"
                                            value="{{ old('translations.en.title') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('title_ar') }}</label>
                                        <input type="text" name="translations[ar][title]"
                                            value="{{ old('translations.ar.title') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    </div>
                                </div>
                                <div class="option-section" style="display: none;">
                                    <div id="options-container" class="mt-4">
                                        <h4 class="text-lg font-medium mb-2">{{ __('options') }}</h4>
                                        <div class="main-option-item border p-4 rounded mb-4">
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-6">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700">{{ __('option_title_en') }}</label>
                                                    <input type="text" name="options[0][translations][en][title]"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                                </div>
                                                <div class="col-span-12 md:col-span-6">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700">{{ __('option_title_ar') }}</label>
                                                    <input type="text" name="options[0][translations][ar][title]"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4" id="add-option-container">
                                        <button type="button" id="add-option"
                                            class="flex items-center gap-2 px-3 py-2 text-white bg-success hover:bg-blue-600 rounded-lg shadow">
                                            <i class="las la-plus-circle"></i> {{ __('add_option') }}
                                        </button>
                                    </div>
                                </div>
                                <div class="option-range mt-3" style="display: none;">
                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-6 md:col-span-6">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('min') }}</label>
                                            <input type="number" name="min_value"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                        </div>
                                        <div class="col-span-6 md:col-span-6">
                                            <label
                                                class="block text-sm font-medium text-gray-700">{{ __('max') }}</label>
                                            <input type="number" name="max_value"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                        </div>
                                    </div>

                                </div>

                                <div class="mt-4 flex justify-center">
                                    <button type="submit"
                                        class="px-6 py-2 text-white bg-primary hover:bg-primary-700 rounded-md shadow-sm">
                                        <i class="las la-save"></i> {{ __('save') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\Admin\FilterRequest') !!}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
        });
    </script>

    <script>
        let optionCount = 1;

        $('#add-option').click(function() {
            const template = `
                <div class="option-item border p-4 rounded mb-4">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">{{ __('option_title_en') }}</label>
                            <input type="text" name="options[${optionCount}][translations][en][title]" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                        <div class="col-span-12 md:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">{{ __('option_title_ar') }}</label>
                            <input type="text" name="options[${optionCount}][translations][ar][title]"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        </div>
                    </div>
                    <button type="button" class="flex items-center gap-2 px-3 py-2 text-white bg-danger rounded-lg shadow remove-option mt-3">
                        <i class="las la-trash"></i> {{ __('remove') }}
                    </button>
                </div>
         `;

            $('#options-container').append(template);
            optionCount++;
        });

        $(document).on('change', '#type', function() {
            $('.option-section').hide();
            $('.option-range').hide();
            $('.option-item').remove();
            if ($(this).val() === 'dropdown' || $(this).val() === 'dropdown_multiple') {
                $('.option-section').show();
                $('.option-range').hide();
            } else if ($(this).val() === 'range') {
                $('.option-range').show();
                $('.option-section').hide();
            } else {
                $('.option-section').hide();
                $('.option-range').hide();
            }
        });
        $(document).on('click', '.remove-option', function() {
            $(this).closest('.option-item').remove();
        });
    </script>
@endpush
