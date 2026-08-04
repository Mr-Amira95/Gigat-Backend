@extends('layouts.master')
@section('title', __('freelancers'))
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
                    <h3 class="text-[1.125rem] font-semibold">{{ __('freelancers') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('freelancers') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('freelancers') }}</h5>
                            <div class="ms-auto flex items-center gap-2">
                                <a href="{{ route('freelancers.archived') }}"
                                    class="flex items-center gap-2 px-4 py-2 text-white bg-success hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-archive"></i> {{ __('archived_freelancers') }}
                                </a>
                                @can('create_freelancers')
                                    <a href="{{ route('freelancers.create') }}"
                                        class="flex items-center gap-2 px-4 py-2 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                        <i class="las la-plus-circle text-lg"></i>{{ __('create_freelancers') }}
                                    </a>
                                @endcan
                                <a href="javascript:void(0)" id="filter-btn"
                                    class="flex items-center gap-2 px-4 py-2 text-white bg-secondary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-filter"></i> {{ __('filter') }}
                                </a>
                            </div>
                        </div>
                        <div class="box-footer border-t p-4" style="display: none;">
                            <h6 class="font-bold mb-2">{{ __('filter') }}</h6>
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
                                <select id="activation-status-filter"
                                    class="w-48 px-2 py-2 mt-1 block rounded-lg  border-gray-300 text-gray-500">
                                    <option value="">{{ __('all_statuses') }}</option>
                                    <option value="active"
                                        {{ request()->get('activation_status') == 'active' ? 'selected' : '' }}>
                                        {{ __('active') }}</option>
                                    <option value="inactive"
                                        {{ request()->get('activation_status') == 'inactive' ? 'selected' : '' }}>
                                        {{ __('inactive') }}</option>
                                </select>
                                <div class="w-48">
                                    <label for="created-date-from"
                                        class="text-sm font-medium text-gray-700 mb-1 block">{{ __('from_date') }}</label>
                                    <input type="date" id="created-date-from" name="created_date_from"
                                        value="{{ request()->get('created_date_from') }}"
                                        class="w-48 px-2 py-2 mt-1 block rounded-lg border-gray-300">
                                </div>
                                <div class="w-48">
                                    <label for="created-date-to"
                                        class="text-sm font-medium text-gray-700 mb-1 block">{{ __('to_date') }}</label>
                                    <input type="date" id="created-date-to" name="created_date_to"
                                        value="{{ request()->get('created_date_to') }}"
                                        class="w-48 px-2 py-2 mt-1 block rounded-lg border-gray-300">
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
                                        <th>{{ __('bank_details') }}</th>
                                        <th>{{ __('file') }}</th>
                                        <th>{{ __('created_at') }}</th>
                                        <th>{{ __('verified') }}</th>
                                        <th>{{ __('account_status') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($freelancers as $freelancer)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($freelancer->avatar)
                                                    <img class="rounded-full w-16 h-16"
                                                        src="{{ asset($freelancer->avatar) }}" alt="">
                                                @endif
                                            </td>
                                            <td>{{ $freelancer->username }}</td>
                                            <td>{{ $freelancer->email }}</td>
                                            <td>{{ $freelancer->full_phone }}</td>
                                            <td>{{ $freelancer->profession->translation->title }}</td>
                                            <td>
<a href="{{ route('freelancers.bankDetailsPage', $freelancer->id) }}"
   class="flex items-center justify-center gap-2 px-4 py-2 text-white bg-success hover:bg-blue-600 rounded-lg shadow">
    {{ __('show') }}
</a>                                            </td>
                                            <td>
                                                @if ($freelancer->freelancer && $freelancer->freelancer->file)
                                                    <a href="{{ asset($freelancer->freelancer->file) }}" target="_blank">
                                                        Open File
                                                    </a>
                                                @else
                                                    No File
                                                @endif
                                            </td>
                                            <td>{{ $freelancer->created_at?->format('Y-m-d') }}</td>

                                            <td>
                                                @can('verify_freelancers')
                                                    <div class="flex items-center justify-center">
                                                        <input type="checkbox"
                                                            class="ti-switch verification shrink-0 !w-11 !h-6 before:size-5 verify-switch"
                                                            data-item-id="{{ $freelancer->freelancer->id }}"
                                                            data-route="{{ route('freelancers.updateVerification') }}"
                                                            {{ $freelancer->freelancer->status == 'verified' ? 'checked' : '' }}>
                                                    </div>
                                                @else
                                                    <div class="flex items-center justify-center">
                                                        <input disabled type="checkbox"
                                                            class="ti-switch verification shrink-0 !w-11 !h-6 before:size-5 verify-switch"
                                                            data-item-id="{{ $freelancer->freelancer->id }}"
                                                            data-route="{{ route('freelancers.updateVerification') }}"
                                                            {{ $freelancer->freelancer->status == 'verified' ? 'checked' : '' }}>
                                                    </div>
                                                @endcan
                                            </td>
                                            <td>
                                                @can('delete_freelancers')
                                                    <div class="flex items-center justify-center">
                                                        <input type="checkbox" id="hs-small-switch-{{ $freelancer->id }}"
                                                            class="ti-switch shrink-0 !w-11 !h-6 before:size-5"
                                                            data-item-id="{{ $freelancer->id }}"
                                                            data-route="{{ route('freelancers.updateActivation') }}"
                                                            {{ $freelancer->is_active ? 'checked' : '' }}>
                                                    </div>
                                                @else
                                                    <div class="flex items-center justify-center">
                                                        <input disabled type="checkbox"
                                                            id="hs-small-switch-{{ $freelancer->id }}"
                                                            class="ti-switch shrink-0 !w-11 !h-6 before:size-5"
                                                            data-item-id="{{ $freelancer->id }}"
                                                            data-route="{{ route('freelancers.updateActivation') }}"
                                                            {{ $freelancer->is_active ? 'checked' : '' }}>
                                                    </div>
                                                @endcan
                                            </td>
                                            <td>
                                                {{-- <a aria-label="anchor"
                                                    href="{{ route('freelancers.show', $freelancer->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-info mx-1 rounded-pill">
                                                    <i class="las la-eye"></i>
                                                </a> --}}

                                                @can('view_freelancers')
                                                    <a aria-label="Login as Freelancer"
                                                        href="{{ route('freelancer.login', ['token' => encrypt($freelancer->id)]) }}"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-info mx-1 rounded-pill">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                @endcan

                                                @can('delete_freelancers')
                                                    <a aria-label="anchor" href="javascript:void(0);"
                                                        onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $freelancer->id }})"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                        <i class="las la-trash"></i>
                                                    </a>
                                                    <form id="delete-form-{{ $freelancer->id }}"
                                                        action="{{ route('freelancers.destroy', $freelancer->id) }}"
                                                        method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endcan
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
                    activation_status: $('#activation-status-filter').val(),
                    created_date_from: $('#created-date-from').val(),
                    created_date_to: $('#created-date-to').val(),
                };

                var queryString = $.param(params);
                window.location.href = "{{ route('freelancers.index') }}?" + queryString;
            });
            if (
                '{{ request()->get('username') }}' ||
                '{{ request()->get('email') }}' ||
                '{{ request()->get('phone') }}' ||
                '{{ request()->get('gender') }}' ||
                '{{ request()->get('activation_status') }}' ||
                '{{ request()->get('created_date_from') }}' ||
                '{{ request()->get('created_date_to') }}' ||
                @json(!empty(request()->get('profession'))) ||
                @json(!empty(request()->get('country')))
            ) {
                $('.box-footer').show();
            }
            $('#filter-reset').on('click', function() {
                window.location.href = "{{ route('freelancers.index') }}";
            });
        });
    </script>
    <script>
        $('.verification').on('change', function() {
            var freelancerId = $(this).data('item-id');
            var $toggle = $(this);
            $.ajax({
                url: $toggle.data('route'),
                method: 'POST',
                data: {
                    id: freelancerId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (!response.success) {
                        $toggle.prop('checked', !$toggle.prop('checked'));
                    }
                },
                error: function() {
                    $toggle.prop('checked', !$toggle.prop('checked'));
                }
            });
        });
    </script>
@endpush
