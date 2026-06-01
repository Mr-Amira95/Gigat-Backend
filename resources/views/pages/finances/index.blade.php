@extends('layouts.master')

@section('title', __('finances'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('finances') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('finances') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between align-center">
                            <h5 class="box-title">{{ __('finances') }}</h5>
                            <button id="mark-paid-btn" type="submit" form="bulk-update-form"
                                class="hidden flex items-center gap-2 px-4 py-2 text-white bg-success hover:bg-blue-600 rounded-lg shadow mt-3">
                                {{ __('mark_selected_as_paid') }}
                            </button>
                        </div>

                        {{-- ✅ Filter Section --}}
                        <div class="box-footer border-t p-4">
                            {{-- <h6 class="font-bold mb-4">{{ __('filter_tags') }}</h6> --}}

                            {{-- Grid: 12 columns --}}
                            <div class="grid grid-cols-12 gap-4 mb-4">
                                {{-- Client --}}
                                <div class="col-span-12 md:col-span-4">
                                    <label for="client-filter"
                                        class="text-sm font-medium text-gray-700 mb-1 block">{{ __('client') }}</label>
                                    <select id="client-filter"
                                        class="select2 w-full px-2 py-2 rounded-lg border-gray-300 text-gray-500">
                                        <option value="">{{ __('all_clients') }}</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"
                                                {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Freelancer --}}
                                <div class="col-span-12 md:col-span-4">
                                    <label for="freelancer-filter"
                                        class="text-sm font-medium text-gray-700 mb-1 block">{{ __('freelancer') }}</label>
                                    <select id="freelancer-filter"
                                        class="select2 w-full px-2 py-2 rounded-lg border-gray-300 text-gray-500">
                                        <option value="">{{ __('all_freelancers') }}</option>
                                        @foreach ($freelancers as $freelancer)
                                            <option value="{{ $freelancer->id }}"
                                                {{ request('freelancer_id') == $freelancer->id ? 'selected' : '' }}>
                                                {{ $freelancer->username }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Payment Status --}}
                                <div class="col-span-12 md:col-span-4">
                                    <label for="status-filter"
                                        class="text-sm font-medium text-gray-700 mb-1 block">{{ __('payment_status') }}</label>
                                    <select id="status-filter"
                                        class="w-full px-2 py-2 rounded-lg border-gray-300 text-gray-500">
                                        <option value="">{{ __('all_statuses') }}</option>
                                        @foreach (\App\Enums\PaymentStatusEnum::cases() as $status)
                                            <option value="{{ $status->value }}"
                                                {{ request('payment_status') == $status->value ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Paid Date From --}}
                                <div class="col-span-12 md:col-span-4">
                                    <label for="paid-date-from"
                                        class="text-sm font-medium text-gray-700 mb-1 block">{{ __('from_date') }}</label>
                                    <input type="date" id="paid-date-from" name="paid_date_from"
                                        value="{{ request('paid_date_from') }}"
                                        class="w-full px-2 py-2 rounded-lg border-gray-300">
                                </div>

                                {{-- Paid Date To --}}
                                <div class="col-span-12 md:col-span-4">
                                    <label for="paid-date-to"
                                        class="text-sm font-medium text-gray-700 mb-1 block">{{ __('to_date') }}</label>
                                    <input type="date" id="paid-date-to" name="paid_date_to"
                                        value="{{ request('paid_date_to') }}"
                                        class="w-full px-2 py-2 rounded-lg border-gray-300">
                                </div>

                                {{-- Buttons --}}
                                <div class="col-span-12 md:col-span-4 flex items-end gap-2">
                                    <a href="javascript:void(0)" id="filter-submit"
                                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600">
                                        {{ __('submit_filter') }}
                                    </a>

                                    <a href="javascript:void(0)" id="filter-reset"
                                        class="px-4 py-2 bg-danger text-white rounded-lg hover:bg-red-600">
                                        {{ __('reset_filter') }}
                                    </a>

                                    <a href="{{ route('finances.export', request()->query()) }}"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                        <i class="ti ti-download me-1"></i> {{ __('export_csv') }}
                                    </a>
                                </div>
                            </div>
                        </div>





                        <div class="box-body">
                            <form id="bulk-update-form" method="POST" action="{{ route('finances.bulkUpdate') }}">
                                @csrf

                                <table id="basic-table" class="table text-center">
                                    <thead>
                                        <tr>
                                            @can('pay_function')
                                                <th><input type="checkbox" id="select-all"
                                                        class="rounded-sm border-gray-800 text-primary focus:ring-primary"></th>
                                            @endcan
                                            <th>#</th>
                                            <th>{{ __('request_id') }}</th>
                                            <th>{{ __('freelancer') }}</th>
                                            <th>{{ __('bank_details') }}</th>
                                            <th>{{ __('amount') }}</th>
                                            <th>{{ __('payment_status') }}</th>
                                            <th>{{ __('paid_at') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($finances as $finance)
                                            <tr>
                                                @can('pay_function')
                                                    <td>
                                                        @if ($finance->payment_status !== \App\Enums\PaymentStatusEnum::PAID->value)
                                                            <input type="checkbox" name="finance_ids[]"
                                                                value="{{ $finance->id }}"
                                                                class="rounded-sm border-gray-800 text-primary focus:ring-primary single-checkbox">
                                                        @endif
                                                    </td>
                                                @endcan
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <a class="text-primary underline"
                                                        href="{{ route('requests.show', $finance->request->id) }}">
                                                        {{ $finance->request->order_number }}
                                                    </a>
                                                </td>
                                                <td>{{ $finance->request->service->user->username }}</td>
                                                <td>
<a href="{{ route('freelancers.bankDetailsPage', optional($finance->request?->service?->user)->id ?? 0) }}"
   class="flex items-center justify-center gap-2 px-4 py-2 text-white bg-success hover:bg-blue-600 rounded-lg shadow">
    {{ __('show') }}
</a>
                                                </td>
                                                {{-- <td>{{ number_format($finance->amount, 2) }}</td> --}}
                                                <td> {{ \App\Utilities\CurrencyConverter::convert(($finance->amount ?? 0) - ($finance->commission ?? 0), 'USD', $currentCurrency) }}
                                                </td>
                                                <td>{!! \App\Enums\PaymentStatusEnum::tryFrom($finance->payment_status)?->badge() !!}</td>
                                                <td>{{ $finance->paid_at ?? '-' }}</td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </form>
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
            var lastColumnIndex = {{ auth()->user()->can('pay_function') ? 7 : 6 }};

            const $markBtn = $('#mark-paid-btn');

            function toggleButtonVisibility() {
                if ($('.single-checkbox:checked').length > 0) {
                    $markBtn.removeClass('hidden');
                } else {
                    $markBtn.addClass('hidden');
                }
            }

            $('#basic-table').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [0, lastColumnIndex]
                }]
            });

            $('#select-all').on('change', function() {
                const checked = this.checked;
                $('.single-checkbox').prop('checked', checked);
                toggleButtonVisibility();
            });

            $(document).on('change', '.single-checkbox', function() {
                const all = $('.single-checkbox').length;
                const checkedCount = $('.single-checkbox:checked').length;

                $('#select-all').prop('checked', all > 0 && all === checkedCount);
                toggleButtonVisibility();
            });

            toggleButtonVisibility();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#filter-submit').click(function() {
                const query = new URLSearchParams();

                if ($('#client-filter').val()) query.append('client_id', $('#client-filter').val());
                if ($('#freelancer-filter').val()) query.append('freelancer_id', $('#freelancer-filter')
                    .val());
                if ($('#status-filter').val()) query.append('payment_status', $('#status-filter').val());
                if ($('#paid-date-from').val()) query.append('paid_date_from', $('#paid-date-from').val());
                if ($('#paid-date-to').val()) query.append('paid_date_to', $('#paid-date-to').val());

                const baseUrl = "{{ route('finances.index') }}";
                window.location.href = `${baseUrl}?${query.toString()}`;
            });

            $('#filter-reset').click(function() {
                window.location.href = "{{ route('finances.index') }}";
            });
        });
    </script>

@endpush
