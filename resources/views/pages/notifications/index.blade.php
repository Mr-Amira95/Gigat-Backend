@extends('layouts.master')
@section('title', __('notifications'))
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
                    <h3 class="text-[1.125rem] font-semibold">{{ __('notifications') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('notifications') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('notifications') }}</h5>
                            <div class="ms-auto flex items-center gap-2">
                                @can('send_notifications')
                                    <a href="{{ route('notifications.create') }}"
                                        class="flex items-center gap-2 px-4 py-2 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                        <i class="las la-plus-circle text-lg"></i>{{ __('send_notifications') }}
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="box-body">
                            <table id="basic-table" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('title') }}</th>
                                        <th>{{ __('message') }}</th>
                                        <th># {{ __('users') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notifications as $notification)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ json_decode($notification->title)->{app()->getLocale()} ?? '-' }}</td>

                                            <td>{{ json_decode($notification->body)->{app()->getLocale()} ?? '-' }}</td>

                                            <td>{{ $notification->total }}</td>
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
                window.location.href = "{{ route('notifications.index') }}?" + queryString;
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
                window.location.href = "{{ route('notifications.index') }}";
            });
        });
    </script>
    {{-- <script>
        $('.verification').on('change', function() {
            var freelancerId = $(this).data('item-id');
            var statusValue = $(this).is(':checked') ? 'verified' : 'unverified';

            $.ajax({
                url: $(this).data('route'),
                method: 'POST',
                data: {
                    id: freelancerId,
                    status: statusValue,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Verification status updated');
                }
            });
        });
    </script> --}}
@endpush
