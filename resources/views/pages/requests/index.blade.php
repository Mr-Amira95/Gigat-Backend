@extends('layouts.master')
@section('title', __('requests'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('requests') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('requests') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('requests') }}</h5>
                        </div>
                        <div class="box-footer border-t p-4">
                            <div class="grid grid-cols-12 gap-4 mb-4">
                                {{-- Status Filter --}}
                                <div class="col-span-12 md:col-span-4">
                                    <label for="status-filter" class="text-sm font-medium text-gray-700 mb-1 block">
                                        {{ __('status') }}
                                    </label>
                                    <select id="status-filter"
                                        class="w-full px-2 py-2 rounded-lg border-gray-300 text-gray-500">
                                        <option value="">{{ __('all_statuses') }}</option>
                                        @foreach (\App\Enums\RequestStatusEnum::cases() as $status)
                                            <option value="{{ $status->value }}"
                                                {{ request('status') == $status->value ? 'selected' : '' }}>
                                                {{ $status->label() }}
                                            </option>
                                        @endforeach
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
                            <table id="basic-table" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('order_number') }}</th>
                                        <th>{{ __('title') }}</th>
                                        <th>{{ __('status') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requests as $request)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $request->order_number }}</td>
                                            <td>{{ $request->translation->title }}</td>
                                            <td>
                                                {!! \App\Enums\RequestStatusEnum::tryFrom($request->status)?->badge() !!}
                                            </td>
                                            <td>

                                                <a aria-label="anchor" href="{{ route('requests.show', $request->id) }}"
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
    <script>
        $(document).ready(function() {
            $('#basic-table').DataTable();
        });

        // ✅ Status filter submit
        $('#filter-submit').click(function() {
            const query = new URLSearchParams();
            if ($('#status-filter').val()) query.append('status', $('#status-filter').val());

            const baseUrl = "{{ route('requests.index') }}";
            window.location.href = `${baseUrl}?${query.toString()}`;
        });

        // ✅ Reset filter
        $('#filter-reset').click(function() {
            window.location.href = "{{ route('requests.index') }}";
        });
    </script>
@endpush
