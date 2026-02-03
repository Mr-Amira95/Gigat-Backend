@extends('layouts.master')
@section('title', __('edit_filter'))
@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('edit_filter') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary hover:text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold text-primary">{{ __('edit_filter') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('edit_filter') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('filters.update', $filter->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('category') }}</label>
                                        <select name="category_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $filter->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->translation->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('type') }}</label>
                                        <select name="type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            @foreach (App\Enums\FilterTypeEnum::cases() as $type)
                                                <option value="{{ $type->value }}"
                                                    {{ old('type', $filter->type) == $type->value ? 'selected' : '' }}>
                                                    {{ $type->label() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('title_en') }}</label>
                                        <input type="text" name="translations[en][title]"
                                            value="{{ old('translations.en.title', $filter->translation->where('language', 'en')->first()?->title) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    </div>

                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('title_ar') }}</label>
                                        <input type="text" name="translations[ar][title]"
                                            value="{{ old('translations.ar.title', $filter->translation->where('language', 'ar')->first()?->title) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    </div>
                                </div>

                                <div id="options-container" class="mt-4">
                                    <h4 class="text-lg font-medium mb-2">{{ __('options') }}</h4>
                                    @foreach ($filter->options as $index => $option)
                                        <div class="option-item border p-4 rounded mb-4">
                                            <input type="hidden" name="options[{{ $index }}][id]"
                                                value="{{ $option->id }}">
                                            <div class="grid grid-cols-12 gap-4">
                                                <div class="col-span-12 md:col-span-6">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700">{{ __('option_title_en') }}</label>
                                                    <input type="text"
                                                        name="options[{{ $index }}][translations][en][title]"
                                                        value="{{ $option->translation->where('language', 'en')->first()?->title }}"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                                </div>
                                                <div class="col-span-12 md:col-span-6">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700">{{ __('option_title_ar') }}</label>
                                                    <input type="text"
                                                        name="options[{{ $index }}][translations][ar][title]"
                                                        value="{{ $option->translation->where('language', 'ar')->first()?->title }}"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                                </div>
                                            </div>
                                            <button type="button"
                                                class="flex items-center gap-2 px-3 py-2 text-white bg-danger rounded-lg shadow remove-option mt-3">
                                                <i class="las la-trash"></i> {{ __('remove') }}
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4">
                                    <button type="button" id="add-option"
                                        class="flex items-center gap-2 px-3 py-2 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                        <i class="las la-plus-circle"></i> {{ __('add_option') }}
                                    </button>
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

    <script>
        let optionCount = {{ $filter->options->count() }};

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

        $(document).on('click', '.remove-option', function() {
            $(this).closest('.option-item').remove();
        });
    </script>
@endpush
