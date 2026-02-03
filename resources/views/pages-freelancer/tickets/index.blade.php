@extends('layouts.master')
@section('title', __('tickets'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('tickets') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('freelancer.home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('tickets') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between items-center">
                            <h5 class="box-title">{{ __('tickets') }}</h5>
                            <a href="{{ route('freelancer.tickets.create') }}"
                                class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                <i class="las la-plus-circle text-lg"></i>{{ __('add_ticket') }}
                            </a>
                            {{-- <a href="javascript:void(0);" style="white-space: nowrap;" id="filter-btn"
                                class="flex items-center gap-2 px-4 py-3 text-white bg-success hover:bg-blue-600 rounded-lg shadow mx-2">
                                <i class="las la-filter text-lg"></i> {{ __('filter') }}
                            </a> --}}
                        </div>
                        <div class="box-footer border-t p-4" style="display: none;">
                            <h6 class="font-bold mb-2">{{ __('filter_tickets') }}</h6>
                            <div class="flex flex-wrap gap-4">
                                <select id="category-filter" class="w-48 px-2 py-2 border rounded-lg">
                                    <option value="">{{ __('all_categories') }}</option>
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
                                        <th>{{ __('code') }}</th>
                                        {{-- <th>{{ __('user') }}</th> --}}
                                        <th>{{ __('subject') }}</th>
                                        <th>{{ __('status') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tickets as $ticket)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $ticket->code }}</td>
                                            {{-- <td>{{ $ticket->user->username }}</td> --}}
                                            <td>{{ $ticket->subject }}</td>
                                            <td>
                                                {!! \App\Enums\TicketStatusEnum::tryFrom($ticket->status)?->badge() !!}
                                            </td>

                                            <td>
                                                <a href="{{ route('freelancer.tickets.show', $ticket->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                    <i class="las la-eye"></i>
                                                </a>
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

            // Load categories first
            fetchCategories(categoryId);

            // If a category is selected, fetch its subcategories
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

            function fetchCategories(selectedCategoryId = null) {
                $.ajax({
                    url: "{{ url('/api/v1/categories') }}",
                    type: "GET",
                    success: function(response) {
                        let options = '<option value="">{{ __('all_categories') }}</option>';
                        response.data.forEach(category => {
                            let selected = category.id == selectedCategoryId ? 'selected' : '';
                            options +=
                                `<option value="${category.id}" ${selected}>${category.title}</option>`;
                        });
                        $('#category-filter').html(options);
                    },
                    error: function() {
                        console.error('Failed to fetch categories.');
                    }
                });
            }

            function fetchSubCategories(categoryId) {
                if (!categoryId) {
                    $('#subcategory-filter').html('<option value="">{{ __('all_sub_categories') }}</option>');
                    return;
                }
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
                    currentUrl.searchParams.delete('category_id');
                }

                if (subcategoryId) {
                    currentUrl.searchParams.set('subcategory_id', subcategoryId);
                } else {
                    currentUrl.searchParams.delete('subcategory_id');
                }

                window.location.href = currentUrl.toString();
            });
        });

        // $(document).ready(function() {
        //     $('#basic-table').DataTable();
        //     var categoryId = "{{ request('category_id') }}";
        //     var subcategoryId = "{{ request('subcategory_id') }}";
        //     fetchSubCategories(categoryId);
        //     if (categoryId || subcategoryId) {
        //         fetchSubCategories(categoryId);
        //         $('.box-footer').show();
        //     }
        //     $('#filter-btn').on('click', function() {
        //         $('.box-footer').stop().slideToggle();
        //     });
        //     $('#category-filter').on('change', function() {
        //         const categoryId = $(this).val();
        //         fetchSubCategories(categoryId);
        //     });

        //     function fetchSubCategories(categoryId) {
        //         $.ajax({
        //             url: "{{ url('/api/v1/sub-categories') }}",
        //             type: "GET",
        //             data: {
        //                 category_id: categoryId
        //             },
        //             success: function(response) {
        //                 let options = '<option value="">{{ __('all_sub_categories') }}</option>';
        //                 let selectedSubcategoryId = "{{ request('subcategory_id') }}";
        //                 response.data.forEach(subcategory => {
        //                     let selected = subcategory.id == selectedSubcategoryId ?
        //                         'selected' : '';
        //                     options +=
        //                         `<option value="${subcategory.id}" ${selected}>${subcategory.title}</option>`;
        //                 });
        //                 $('#subcategory-filter').html(options);
        //             },
        //             error: function() {
        //                 console.error('Failed to fetch subcategories.');
        //             }
        //         });
        //     }
        //     $('#filter-submit').on('click', function() {
        //         const categoryId = $('#category-filter').val();
        //         const subcategoryId = $('#subcategory-filter').val();
        //         const currentUrl = new URL(window.location.href);
        //         if (categoryId !== "") {
        //             currentUrl.searchParams.set('category_id', categoryId);
        //         } else {
        //             currentUrl.searchParams.set('category_id', ""); // Set to empty instead of deleting
        //         }
        //         if (subcategoryId) {
        //             currentUrl.searchParams.set('subcategory_id', subcategoryId);
        //         } else {
        //             currentUrl.searchParams.delete('subcategory_id');
        //         }
        //         window.location.href = currentUrl.toString();
        //     });
        // });
    </script>
@endpush
