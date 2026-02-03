@extends('layouts.master')
@section('title', __('tags'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('tags') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('tags') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
              @if (session('error_message'))
                @php
                    $errorMessage = session('error_message');
                    $messageParts = explode('|', $errorMessage);

                    $message = $messageParts[0];
                @endphp

                <div class="alert alert-danger flex justify-between items-center">
                    <span>{{ $message }}</span>
                 
                </div>
            @endif
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between items-center">
                            <h5 class="box-title">{{ __('tags') }}</h5>
                            <a href="{{ route('tags.create') }}" style="white-space: nowrap;"
                                class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                <i class="las la-plus-circle text-lg"></i> {{ __('add_tag') }}
                            </a>
                            <a href="javascript:void(0);" style="white-space: nowrap;" id="filter-btn"
                                class="flex items-center gap-2 px-4 py-3 text-white bg-success hover:bg-blue-600 rounded-lg shadow mx-2">
                                <i class="las la-filter text-lg"></i> {{ __('filter') }}
                            </a>
                        </div>
                        <div class="box-footer border-t p-4" style="display: none;">
                            <h6 class="font-bold mb-2">{{ __('filter_tags') }}</h6>
                            <div class="flex flex-wrap gap-4">
                                <select id="category-filter" class="w-48 px-2 py-2 border rounded-lg">
                                    <option value="">{{ __('all_categories') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->translation->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <select id="subcategory-filter" class="w-48 px-2 py-2 border rounded-lg">
                                    <option value="">{{ __('all_sub_categories') }}</option>
                                </select>
                                <button id="filter-submit"
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600">
                                    {{ __('submit_filter') }}
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <table id="basic-table" class="table text-center">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('title') }}</th>
                                        <th>{{ __('category') }}</th>
                                        <th>{{ __('tag_slug') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tags as $tag)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $tag->translation->title }}</td>
                                            <td>{{ $tag->subCategory ? $tag->subCategory->translation->title : __('no_subCategory') }}
                                            </td>
                                            <td>{{ $tag->slug }}</td>
                                            <td>
                                                <a href="{{ route('tags.edit', $tag->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <a href="javascript:void(0);"
                                                    onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $tag->id }})"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                    <i class="las la-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $tag->id }}"
                                                    action="{{ route('tags.destroy', $tag->id) }}" method="POST"
                                                    class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @if (app()->getLocale() == 'en')
        <script src="{{ asset('build/assets/datatable/datatables-en.min.js') }}"></script>
    @else
        <script src="{{ asset('build/assets/datatable/datatables-ar.min.js') }}"></script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#basic-table').DataTable();
            var categoryId = "{{ request('category_id') }}";
            var subcategoryId = "{{ request('subcategory_id') }}";
            fetchSubCategories(categoryId);

            if (categoryId || subcategoryId) {
                fetchSubCategories(categoryId);
                $('.box-footer').show();
            }
            $('#filter-btn').on('click', function() {
                $('.box-footer').stop().slideToggle();
            });
            $('#category-filter').on('change', function() {
                const categoryId = $(this).val();
                fetchSubCategories(categoryId);
            });

            function fetchSubCategories(categoryId) {
                $.ajax({
                    url: "{{ url('/api/v1/sub-categories') }}",
                    type: "GET",
                    data: {
                        category_id: categoryId
                    },
                    success: function(response) {
                        let options = '<option value="">{{ __('all_sub_categories') }}</option>';
                        let selectedSubcategoryId = "{{ request('subcategory_id') }}";
                        response.data.forEach(subcategory => {
                            let selected = subcategory.id == selectedSubcategoryId ?
                                'selected' : '';
                            options +=
                                `<option value="${subcategory.id}" ${selected}>${subcategory.title}</option>`;
                        });
                        $('#subcategory-filter').html(options);
                    },
                    error: function() {
                        console.error('Failed to fetch subcategories.');
                    }
                });
            }
            $('#filter-submit').on('click', function() {
                const categoryId = $('#category-filter').val();
                const subcategoryId = $('#subcategory-filter').val();
                const currentUrl = new URL(window.location.href);
                if (categoryId !== "") {
                    currentUrl.searchParams.set('category_id', categoryId);
                } else {
                    currentUrl.searchParams.set('category_id', ""); // Set to empty instead of deleting
                }
                if (subcategoryId) {
                    currentUrl.searchParams.set('subcategory_id', subcategoryId);
                } else {
                    currentUrl.searchParams.delete('subcategory_id');
                }
                window.location.href = currentUrl.toString();
            });
        });
    </script>
@endpush
