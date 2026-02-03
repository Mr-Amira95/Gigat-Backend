@extends('layouts.master')
@section('title', __('services'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('services') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('freelancer.home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('services') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between align-center">
                            <h5 class="box-title">{{ __('services') }}</h5>
                            <div class="ms-auto flex items-center gap-2">
                                <a href="{{ route('freelancer.services.create') }}"
                                    class="flex items-center gap-2 px-4 py-2 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-plus-circle text-lg"></i>{{ __('create_service') }}
                                </a>
                                {{-- <a href="javascript:void(0)" id="filter-btn"
                                    class="flex items-center gap-2 px-4 py-2 text-white bg-secondary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-filter"></i> {{ __('filter') }}
                                </a> --}}
                            </div>
                        </div>
                        <div class="box-footer border-t p-4" style="display: none;">
                            <h6 class="font-bold mb-2">{{ __('filter_tags') }}</h6>
                            <div class="flex flex-wrap gap-4">

                                <div class="w-48 " style="margin-top: 7px;">
                                    <select id="category-filter"
                                        class="js-example-basic-multiple w-48 px-2 py-4 mt-1 block rounded-lg  border-gray-300 text-gray-500"
                                        multiple>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ request()->get('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->translation->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-48 mx-3" style="margin-top: 7px;">

                                    <select id="freelancer-filter"
                                        class="js-example-basic-multiple w-48 px-2 py-2 mt-1 block rounded-lg  border-gray-300 text-gray-500"
                                        multiple>
                                        @foreach ($freelancers as $freelancer)
                                            <option value="{{ $freelancer->id }}"
                                                {{ request()->get('freelancer') == $freelancer->id ? 'selected' : '' }}>
                                                {{ $freelancer->username }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <a href="javascript:void(0)" id="filter-submit"
                                    class="flex justify-center items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600">
                                    {{ __('submit_filter') }}
                                </a>
                                <a href="javascript:void(0)" id="filter-reset"
                                    class="flex justify-center items-center px-4 py-2 bg-danger text-white rounded-lg hover:bg-blue-600">
                                    {{ __('reset_filter') }}
                                </a>


                            </div>
                        </div>
                        <div class="box-body">
                            <table id="basic-table" class="table text-center">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('title') }}</th>
                                        <th>{{ __('category') }}</th>
                                        {{-- <th>{{ __('freelancer') }}</th> --}}
                                        {{-- <th>{{ __('recommended') }}</th> --}}

                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $service)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $service->translation->title }}</td>
                                            <td>{{ $service->subCategory->translation->title }}</td>
                                            {{-- <td>
                                                <div class="flex items-center justify-center">
                                                    <input type="checkbox" id="hs-small-switch-{{ $service->id }}"
                                                        class="ti-switch shrink-0 !w-11 !h-6 before:size-5"
                                                        data-item-id="{{ $service->id }}"
                                                        data-route="{{ route('freelancer.services.toggleRecommended') }}"
                                                        {{ $service->is_recommended ? 'checked' : '' }}>
                                                </div>
                                            </td> --}}

                                            {{-- <td>{{ $service->user->username }}</td> --}}
                                            <td>

                                                <a aria-label="anchor"
                                                    href="{{ route('freelancer.services.edit', $service->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <a aria-label="anchor"
                                                    href="{{ route('freelancer.services.show', $service->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-primary mx-1 rounded-pill">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                <a aria-label="anchor" href="javascript:void(0);"
                                                    onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $service->id }})"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                    <i class="las la-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $service->id }}"
                                                    action="{{ route('freelancer.services.destroy', $service->id) }}"
                                                    method="POST" class="hidden">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @if (app()->getLocale() == 'en')
        <script src="{{ asset('build/assets/datatable/datatables-en.min.js') }}"></script>
    @else
        <script src="{{ asset('build/assets/datatable/datatables-ar.min.js') }}"></script>
    @endif
    <script>
        $(document).ready(function() {
            $('#basic-table').DataTable();


            $('#filter-btn').on('click', function() {
                $('.box-footer').stop().slideToggle();
            });
            $('#category-filter').select2({
                placeholder: "{{ __('all_categories') }}",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
            $('#freelancer-filter').select2({
                placeholder: "{{ __('all_freelancers') }}",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
            $('#filter-submit').on('click', function() {
                var params = {
                    category: $('#category-filter').val(),
                    freelancer: $('#freelancer-filter').val(),
                };

                var queryString = $.param(params);
                window.location.href = "{{ route('freelancer.services.index') }}?" + queryString;
            });
            if (
                @json(!empty(request()->get('category'))) ||
                @json(!empty(request()->get('freelancer')))
            ) {
                $('.box-footer').show();
            }
            $('#filter-reset').on('click', function() {
                window.location.href = "{{ route('freelancer.services.index') }}";
            });

        });
    </script>

    <script>
        function toggleRecommended(serviceId, status) {
            fetch(`/freelancer/services/toggle-recommended/${serviceId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status: status ? 1 : 0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
@endpush
