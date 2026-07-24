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
                        <div class="box-header">
                            <h5 class="box-title">{{ __('quotations') }}</h5>

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
                                            <td>
                                                <a aria-label="anchor" href="{{ route('quotations.show', $quotation->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                @can('delete_quotations')
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
    @if (app()->getLocale() == 'en')
        <script src="{{ asset('build/assets/datatable/datatables-en.min.js') }}"></script>
    @else
        <script src="{{ asset('build/assets/datatable/datatables-ar.min.js') }}"></script>
    @endif
    <script>
        $(document).ready(function() {
            $('#basic-table').DataTable();
        });
    </script>
@endpush
