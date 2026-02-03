@extends('layouts.master')
@section('title', __('sliders'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('web_sliders') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('web_sliders') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('web_sliders') }}</h5>
                            <a href="{{ route('sliders.createWebSliders') }}"
                                class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                <i class="las la-plus-circle text-lg"></i>{{ __('add_slider') }}
                            </a>
                        </div>
                        <div class="box-body">
                            <!-- Tab Content -->
                            <div class="tab-content">
                                <!-- Web Sliders Tab -->
                                <div class="tab-pane" id="web-tab">
                                    <table id="basic-table" class="table web-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('title') }}</th>
                                                <th>{{ __('status') }}</th>
                                                <th>{{ __('actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sliders as $slider)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $slider->translation?->title }}</td>
                                                    <td>
                                                        <div class="flex items-center justify-center">
                                                            <input type="checkbox" id="hs-small-switch-{{ $slider->id }}"
                                                                class="ti-switch shrink-0 !w-11 !h-6 before:size-5"
                                                                data-item-id="{{ $slider->id }}"
                                                                data-route="{{ route('sliders.updateActivation') }}"
                                                                {{ $slider->is_active ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a aria-label="anchor"
                                                            href="{{ route('sliders.show', $slider->id) }}"
                                                            class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-primary mx-1 rounded-pill">
                                                            <i class="las la-eye"></i>
                                                        </a>
                                                        <a aria-label="anchor"
                                                            href="{{ route('sliders.editWebSliders', $slider->id) }}"
                                                            class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                            <i class="las la-edit"></i>
                                                        </a>
                                                        <a aria-label="anchor" href="javascript:void(0);"
                                                            onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $slider->id }})"
                                                            class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                            <i class="las la-trash"></i>
                                                        </a>
                                                        <form id="delete-form-{{ $slider->id }}"
                                                            action="{{ route('sliders.destroy', $slider->id) }}"
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
            $('.web-table').DataTable();

        });
    </script>
@endpush
