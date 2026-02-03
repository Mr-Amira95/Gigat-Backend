@extends('layouts.master')
@section('title', __('plans'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('plans') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('plans') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('plans') }}</h5>
                            <a href="{{ route('plans.create') }}"
                                class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                <i class="las la-plus-circle text-lg"></i>{{ __('add_plan') }}
                            </a>
                        </div>
                        <div class="box-body">
                            <table id="basic-table" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('title') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($plans as $plan)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $plan->translation?->title }}</td>
                                          
                                            <td>
                                                <a aria-label="anchor" href="{{ route('plans.features',$plan->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-primary mx-1 rounded-pill">
                                                    <i class="las la-list"></i>
                                                </a>
                                                 <a aria-label="anchor" href="{{ route('plans.edit', $plan->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <a aria-label="anchor" href="javascript:void(0);"
                                                    onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $plan->id }})"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                    <i class="las la-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $plan->id }}"
                                                    action="{{ route('plans.destroy', $plan->id) }}" method="POST"
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
        });
    </script>
@endpush
