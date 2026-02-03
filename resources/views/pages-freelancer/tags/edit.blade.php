@extends('layouts.master')
@section('title', __('edit_tag'))

@section('content')
    <div class="content">
        <div class="main-content">
                 @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-tag text-primary"></i>
                        {{ __('edit_tag') }}
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
                        {{ __('edit_tag') }}
                    </li>
                </ol>
            </div>

            <div class="grid grid-cols-12 gap-6 mt-4">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('edit_tag') }}</h5>
                        </div>
                        <div class="box-body">
                            <form action="{{ route('tags.update', $tag->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-12 gap-4">
                                    <!-- Category Selection -->
                                    <div class="col-span-12 md:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('select_category') }}
                                        </label>
                                        <select id="category-filter" name="category_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <option value="">{{ __('select_category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $tag->subCategory->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->translation->title }}
                                                </option>
                                            @endforeach
                                        </select>

                                    </div>

                                    <!-- Subcategory Selection -->
                                    <div class="col-span-12 md:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('select_subcategory') }}
                                        </label>
                                        <select id="subcategory-filter" name="sub_category_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                            <option value="">{{ __('select_subcategory') }}</option>
                                            @foreach ($subcategories as $subcategory)
                                                <option value="{{ $subcategory->id }}"
                                                    {{ old('sub_category_id', $tag->sub_category_id) == $subcategory->id ? 'selected' : '' }}>
                                                    {{ $subcategory->translation->title }}
                                                </option>
                                            @endforeach
                                        </select>

                                    </div>

                                    <!-- English Title -->
                                    <div class="col-span-12 md:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('tag_title_en') }}
                                        </label>
                                        <input type="text" name="translations[en][title]"
                                            value="{{ $tag->translations->firstWhere('language', 'en')?->title }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">

                                    </div>

                                    <!-- Arabic Title -->
                                    <div class="col-span-12 md:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ __('tag_title_ar') }}
                                        </label>
                                        <input type="text" name="translations[ar][title]"
                                            value="{{ $tag->translations->firstWhere('language', 'ar')?->title }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">

                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                    <button type="submit"
                                        class="px-6 py-2 text-white bg-primary hover:bg-primary-700 rounded-md shadow-sm">
                                        <i class="las la-save"></i> {{ __('update') }}
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
    {!! JsValidator::formRequest('App\Http\Requests\Admin\UpdateTagRequest') !!}

    <script>
        $(document).ready(function() {
            $('#category-filter').on('change', function() {
                const categoryId = $(this).val();
                fetchSubCategories(categoryId);
            });

            function fetchSubCategories(categoryId) {
                $.ajax({
                    url: "{{ url('/api/v1/sub-categories') }}",
                    type: "GET",
                    data: { category_id: categoryId },
                    success: function(response) {
                        let options = '<option value="">{{ __('select_subcategory') }}</option>';
                        let selectedSubcategoryId = "{{ old('sub_category_id', $tag->sub_category_id) }}";

                        response.data.forEach(subcategory => {
                            let selected = subcategory.id == selectedSubcategoryId ? 'selected' : '';
                            options += `<option value="${subcategory.id}" ${selected}>${subcategory.title}</option>`;
                        });

                        $('#subcategory-filter').html(options);
                    },
                    error: function() {
                        console.error('Failed to fetch subcategories.');
                    }
                });
            }

            // If a category is already selected, load its subcategories
            let initialCategoryId = $('#category-filter').val();
            if (initialCategoryId) {
                fetchSubCategories(initialCategoryId);
            }
        });
    </script>
@endpush
