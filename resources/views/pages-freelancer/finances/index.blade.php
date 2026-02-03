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
                        <a class="flex items-center text-primary" href="{{ route('freelancer.home.index') }}">
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

                        <div class="box-footer border-t p-4">
                            <div class="grid grid-cols-12 gap-6 mb-6">
                                <div class="col-span-12 md:col-span-4">
                                    <div class="p-4 bg-white rounded-lg shadow">
                                        <h6 class="text-gray-600 text-sm">{{ __('total_income') }}</h6>
                                        <p class="text-xl font-bold">
                                            {{ $currencySymbol }}
                                            {{ \App\Utilities\CurrencyConverter::convert($totalUsd, 'USD', $currentCurrency) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-span-12 md:col-span-4">
                                    <div class="p-4 bg-white rounded-lg shadow">
                                        <h6 class="text-gray-600 text-sm">{{ __('pending_income') }}</h6>
                                        <p class="text-xl font-bold text-yellow-600">
                                            {{ $currencySymbol }}
                                            {{ \App\Utilities\CurrencyConverter::convert($pendingUsd, 'USD', $currentCurrency) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-span-12 md:col-span-4">
                                    <div class="p-4 bg-white rounded-lg shadow">
                                        <h6 class="text-gray-600 text-sm">{{ __('transferred_income') }}</h6>
                                        <p class="text-xl font-bold text-green-600">
                                            {{ $currencySymbol }}
                                            {{ \App\Utilities\CurrencyConverter::convert($transferredUsd, 'USD', $currentCurrency) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-4 mb-4">
                                {{-- Payment Status Filter --}}
                                <div class="col-span-12 md:col-span-4">
                                    <label for="status-filter" class="text-sm font-medium text-gray-700 mb-1 block">
                                        {{ __('filter_payment_status') }}
                                    </label>
                                    <select id="status-filter"
                                        class="w-full px-2 py-2 rounded-lg border-gray-300 text-gray-500">
                                        <option value="">{{ __('all') }}</option>
                                        <option value="paid" {{ $statusFilter == 'paid' ? 'selected' : '' }}>
                                            {{ __('paid') }}
                                        </option>
                                        <option value="unpaid" {{ $statusFilter == 'unpaid' ? 'selected' : '' }}>
                                            {{ __('unpaid') }}
                                        </option>
                                    </select>
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
                                </div>
                            </div>

                        </div>

                        <div class="box-body">



                            {{-- <form method="GET" action="{{ route('freelancer.finances.index') }}" class="mb-4">
                                <label for="payment_status" class="me-2">{{ __('filter_payment_status') }}:</label>
                                <select name="payment_status" id="payment_status" onchange="this.form.submit()"
                                    class="w-48 px-2 py-2 border rounded-lg" style="width: 220px;">
                                    <option value="">{{ __('all') }}</option>
                                    <option value="paid"
                                        {{ isset($statusFilter) && $statusFilter == 'paid' ? 'selected' : '' }}>
                                        {{ __('paid') }}</option>
                                    <option value="unpaid"
                                        {{ isset($statusFilter) && $statusFilter == 'unpaid' ? 'selected' : '' }}>
                                        {{ __('unpaid') }}</option>
                                </select>
                            </form> --}}



                            <form id="bulk-update-form" method="POST"
                                action="{{ route('freelancer.finances.bulkUpdate') }}">
                                @csrf

                                <table id="basic-table" class="table text-center">
                                    <thead>
                                        <tr>
                                            {{-- <th><input type="checkbox" id="select-all" class="rounded-sm border-gray-800 text-primary focus:ring-primary"></th> --}}
                                            <th>#</th>
                                            <th>{{ __('request_id') }}</th>
                                            <th>{{ __('client') }}</th>
                                            {{-- <th>{{ __('freelancer') }}</th> --}}
                                            <th>{{ __('amount') }}</th>
                                            <th>{{ __('payment_status') }}</th>
                                            <th>{{ __('paid_at') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($finances as $finance)
                                            <tr>
                                                {{-- <td>
                                                    @if ($finance->payment_status !== \App\Enums\PaymentStatusEnum::PAID->value)
                                                        <input type="checkbox" name="finance_ids[]" value="{{ $finance->id }}" class="rounded-sm border-gray-800 text-primary focus:ring-primary single-checkbox">
                                                    @endif
                                                </td> --}}
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <a class="text-primary underline"
                                                        href="{{ route('freelancer.requests.show', $finance->request->id) }}">
                                                        {{ $finance->request->order_number }}
                                                    </a>
                                                </td>
                                                <td>{{ $finance->request->user->username }}</td>

                                                <td>{{ $currencySymbol }}
                                                    {{-- {{ \App\Utilities\CurrencyConverter::convert($finance->amount, 'USD', $currentCurrency) }} --}}
                                                    {{ \App\Utilities\CurrencyConverter::convert(($finance->amount ?? 0) - ($finance->commission ?? 0), 'USD', $currentCurrency) }}
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
                    targets: [0, 5]
                }]
            });
            $('#filter-submit').click(function() {
                const query = new URLSearchParams();

                if ($('#status-filter').val()) {
                    query.append('payment_status', $('#status-filter').val());
                }

                const baseUrl = "{{ route('freelancer.finances.index') }}";
                window.location.href = `${baseUrl}?${query.toString()}`;
            });

            $('#filter-reset').click(function() {
                window.location.href = "{{ route('freelancer.finances.index') }}";
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
@endpush
