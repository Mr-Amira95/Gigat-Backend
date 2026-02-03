@extends('layouts.master')
@section('title', __('clients'))
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
                    <h3 class="text-[1.125rem] font-semibold">{{ __('clients') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('clients') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('clients') }}</h5>
                            <div class="ms-auto flex items-center gap-2">
                                <a href="{{ route('clients.archived') }}"
                                    class="flex items-center gap-2 px-4 py-2 text-white bg-success hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-archive"></i> {{ __('archived_clients') }}
                                </a>
                                <a href="{{ route('clients.create') }}"
                                    class="flex items-center gap-2 px-4 py-2 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-plus-circle text-lg"></i>{{ __('create_clients') }}
                                </a>
                                <a href="javascript:void(0)" id="filter-btn"
                                    class="flex items-center gap-2 px-4 py-2 text-white bg-secondary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-filter"></i> {{ __('filter') }}
                                </a>
                            </div>
                        </div>
                        <div class="box-footer border-t p-4" style="display: none;">
                            <h6 class="font-bold mb-2">{{ __('filter_tags') }}</h6>
                            <div class="flex flex-wrap gap-4">
                                <input type="text" id="username-filter" placeholder="{{ __('username') }}"
                                    value="{{ request()->get('username') }}"
                                    class="w-48 px-2 py-2 mt-1 block rounded-lg  border-gray-300">
                                <input type="email" id="email-filter" placeholder="{{ __('email') }}"
                                    value="{{ request()->get('email') }}"
                                    class="w-48 px-2 py-2 mt-1 block rounded-lg  border-gray-300">
                                <input type="text" id="phone-filter" placeholder="{{ __('phone') }}"
                                    value="{{ request()->get('phone') }}"
                                    class="w-48 px-2 py-2 mt-1 block rounded-lg  border-gray-300">
                                <select id="gender-filter"
                                    class="w-48 px-2 py-2 mt-1 block rounded-lg  border-gray-300 text-gray-500">
                                    <option value="">{{ __('all_genders') }}</option>
                                    <option value="male" {{ request()->get('gender') == 'male' ? 'selected' : '' }}>
                                        {{ __('male') }}</option>
                                    <option value="female" {{ request()->get('gender') == 'female' ? 'selected' : '' }}>
                                        {{ __('female') }}</option>
                                </select>
                                <div class="w-48 " style="margin-top: 7px;">
                                    <select id="profession-filter"
                                        class="js-example-basic-multiple w-48 px-2 py-4 mt-1 block rounded-lg  border-gray-300 text-gray-500"
                                        multiple>
                                        @foreach ($professions as $profession)
                                            <option value="{{ $profession->id }}"
                                                {{ !empty(request()->get('profession')) && in_array($profession->id, request()->get('profession')) ? 'selected' : '' }}>
                                                {{ $profession->translation->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-48 mx-3" style="margin-top: 7px;">

                                    <select id="country-filter"
                                        class="js-example-basic-multiple w-48 px-2 py-2 mt-1 block rounded-lg  border-gray-300 text-gray-500"
                                        multiple>
                                        <option value="">{{ __('all_countries') }}</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country['id'] }}"
                                                {{ !empty(request()->get('country')) && in_array($country['id'], request()->get('country')) ? 'selected' : '' }}>
                                                {{ $country['title'] }}</option>
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
                            <table id="basic-table" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('image') }}</th>
                                        <th>{{ __('name') }}</th>
                                        <th>{{ __('email') }}</th>
                                        <th>{{ __('phone') }}</th>
                                        <th>{{ __('profession') }}</th>
                                        <th>{{ __('country') }}</th>
                                        <th>{{ __('status') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clients as $client)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($client->avatar)
                                                    <img class="rounded-full w-16 h-16" src="{{ asset($client->avatar) }}" alt="">
                                                @endif
                                            </td>
                                            <td>{{ $client->username }}</td>
                                            <td>{{ $client->email }}</td>
                                            <td>{{ $client->full_phone }}</td>
                                            <td>{{ $client->profession->translation->title }}</td>
                                            <td>{{ $client->country->title }}</td>
                                            <td>
                                                <div class="flex items-center justify-center">
                                                    <input type="checkbox" id="hs-small-switch-{{ $client->id }}"
                                                        class="ti-switch shrink-0 !w-11 !h-6 before:size-5"
                                                        data-item-id="{{ $client->id }}"
                                                        data-route="{{ route('clients.updateActivation') }}"
                                                        {{ $client->is_active ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <a aria-label="anchor" href="{{ route('clients.show', $client->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-info mx-1 rounded-pill">
                                                    <i class="las la-eye"></i>
                                                </a>

                                                <a aria-label="anchor" href="javascript:void(0);"
                                                    onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $client->id }})"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                    <i class="las la-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $client->id }}"
                                                    action="{{ route('clients.destroy', $client->id) }}" method="POST"
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
            $('#profession-filter').select2({
                placeholder: "{{ __('all_professions') }}",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
            $('#country-filter').select2({
                placeholder: "{{ __('all_countries') }}",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
            $('#filter-submit').on('click', function() {
                var params = {
                    username: $('#username-filter').val(),
                    email: $('#email-filter').val(),
                    phone: $('#phone-filter').val(),
                    gender: $('#gender-filter').val(),
                    profession: $('#profession-filter').val(),
                    country: $('#country-filter').val(),
                };

                var queryString = $.param(params);
                window.location.href = "{{ route('clients.index') }}?" + queryString;
            });
            if (
                '{{ request()->get('username') }}' ||
                '{{ request()->get('email') }}' ||
                '{{ request()->get('phone') }}' ||
                '{{ request()->get('gender') }}' ||
                @json(!empty(request()->get('profession'))) ||
                @json(!empty(request()->get('country')))
            ) {
                $('.box-footer').show();
            }
            $('#filter-reset').on('click', function() {
                window.location.href = "{{ route('clients.index') }}";
            });

        });
    </script>
@endpush
