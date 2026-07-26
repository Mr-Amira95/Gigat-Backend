@extends('layouts.master')
@section('title', __('quotations'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('quotations') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('quotations') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between align-center">
                            <h5 class="box-title">{{ __('quotations') }}</h5>
                            <div class="ms-auto flex items-center gap-2">
                                <a href="javascript:void(0)" id="filter-btn"
                                    class="flex items-center gap-2 px-4 py-2 text-white bg-secondary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-filter"></i> {{ __('filter') }}
                                </a>
                            </div>
                        </div>
                        <div class="box-footer border-t p-4" style="display: none;">
                            <h6 class="font-bold mb-2">{{ __('filter') }}</h6>
                            <div class="flex flex-wrap gap-4">
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
                                        <th>{{ __('title') }}</th>
                                        <th>{{ __('description') }}</th>
                                        <th>{{ __('price') }}</th>
                                        <th>{{ __('status') }}</th>
                                        <th>{{ __('created_at') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quotations as $quotation)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $quotation->translation->title }}</td>
                                            <td>{{ $quotation->translation->description }}</td>
                                            <td>{{ $quotation->price }}</td>
                                            <td>
                                                {!! \App\Enums\QuotationStatusEnum::tryFrom($quotation->status)?->badge() !!}
                                            </td>
                                            <td>{{ $quotation->created_at?->format('Y-m-d') }}</td>
                                            <td>
                                                <a aria-label="anchor" href="{{ route('quotations.show', $quotation->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                <a aria-label="anchor" href="javascript:void(0);"
                                                    onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $quotation->id }})"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                    <i class="las la-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $quotation->id }}"
                                                    action="{{ route('quotations.destroy', $quotation->id) }}" method="POST"
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
    <script>
        $(document).ready(function() {
            $('#basic-table').DataTable();

            $('#filter-btn').on('click', function() {
                $('.box-footer').stop().slideToggle();
            });
            $('#filter-submit').on('click', function() {
                var params = {
                    created_date_from: $('#created-date-from').val(),
                    created_date_to: $('#created-date-to').val(),
                };

                var queryString = $.param(params);
                window.location.href = "{{ route('quotations.index') }}?" + queryString;
            });
            if (
                '{{ request()->get('created_date_from') }}' ||
                '{{ request()->get('created_date_to') }}'
            ) {
                $('.box-footer').show();
            }
            $('#filter-reset').on('click', function() {
                window.location.href = "{{ route('quotations.index') }}";
            });
        });
    </script>
@endpush
