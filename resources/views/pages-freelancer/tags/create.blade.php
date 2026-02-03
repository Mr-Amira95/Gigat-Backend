@extends('layouts.master')
@section('title', __('create_tag'))
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-tag text-primary"></i>
                        {{ __('create_tag') }}
                    </h3>
                </div>
                <ol class="flex items-center whitespace-nowrap min-w-0">
                    <li class="text-sm">
                        <a class="flex items-center font-semibold text-primary hover:text-primary truncate"
                            href="{{ route('tags.index') }}">
                            {{ __('tags') }}
                            <i class="fas fa-chevron-right mx-3 text-gray-400 text-sm rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-sm font-semibold text-gray-700 truncate" aria-current="page">
                        {{ __('create_tag') }}
                    </li>
                </ol>
            </div>

            <div class="grid grid-cols-12 gap-6 mt-4">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('create_tag') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('tags.store') }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-12 gap-6">
                                    <!-- Multi-Category Selection -->
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('select_categories') }}
                                        </label>
                                        <select id="category-filter" name="category_id[]" multiple
                                            class="js-example-basic-multiple mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ collect(old('category_id', request('category_id', [])))->contains($category->id) ? 'selected' : '' }}>
                                                    {{ $category->translation->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Multi-Subcategory Selection -->
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('select_subcategories') }}
                                        </label>
                                        <select id="subcategory-filter" name="sub_category_id[]" multiple
                                            class=" js-example-basic-multiple mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <!-- Options will be loaded by JS -->
                                        </select>
                                        @error('sub_category_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>


                                    <!-- English Title -->
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('tag_title_en') }}
                                        </label>
                                        <input type="text" name="translations[en][title]"
                                            value="{{ old('translations.en.title') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                        @error('translations.en.title')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Arabic Title -->
                                    <div class="col-span-12 md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('tag_title_ar') }}
                                        </label>
                                        <input type="text" name="translations[ar][title]"
                                            value="{{ old('translations.ar.title') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                        @error('translations.ar.title')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
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
    {!! JsValidator::formRequest('App\Http\Requests\Admin\TagRequest') !!}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                placeholder: "{{ __('select_options') }}",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
            function fetchSubCategories(categoryIds) {
                $.ajax({
                    url: "{{ url('sub-categories/sub-categories-by-category-ids') }}",
                    type: "GET",
                    data: { category_ids: categoryIds },

                    success: function(response) {
                        let options = '';
                        let selectedSubcategoryIds = {!! json_encode(old('sub_category_id', request('sub_category_id', []))) !!};

                        response.data.forEach(subcategory => {
                            const selected = selectedSubcategoryIds.includes(subcategory.id.toString()) ? 'selected' : '';
                            options += `<option value="${subcategory.id}" ${selected}>${subcategory.title}</option>`;
                        });

                        $('#subcategory-filter').html(options);
                    },
                    error: function() {
                        console.error('Failed to fetch subcategories.');
                    }
                });
            }

            const initialCategories = $('#category-filter').val();
            if (initialCategories && initialCategories.length) {
                fetchSubCategories(initialCategories);
            }

            $('#category-filter').on('change', function() {
                const selectedCategories = $(this).val();
                fetchSubCategories(selectedCategories);
            });
        });
    </script>
@endpush
