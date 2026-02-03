@extends('layouts.master')
@section('title', __('categories'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('categories') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('categories') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            @if (session('error_message'))
                @php
                    $errorMessage = session('error_message');
                    $messageParts = explode('|', $errorMessage);

                    $message = $messageParts[0];
                    $categoryId = $messageParts[1] ?? null;
                @endphp

                <div class="alert alert-danger flex justify-between items-center">
                    <span>{{ $message }}</span>
                    @if ($categoryId)
                        <a href="{{ route('subCategories.index', ['category_id' => $categoryId]) }}"
                            class="flex items-center gap-2 px-4 py-3 text-white bg-warning hover:bg-blue-600 rounded-lg shadow">
                            <i class="las la-arrow-right"></i>
                        </a>
                    @endif
                </div>
            @endif
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('categories') }}</h5>
                            <a href="{{ route('categories.create') }}"
                                class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                <i class="las la-plus-circle text-lg"></i>{{ __('add_category') }}
                            </a>
                        </div>
                        <div class="box-body">
                            <table id="basic-table" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('icon') }}</th>
                                        <th>{{ __('title') }}</th>
                                        <th>{{ __('status') }}</th>
                                        <th>{{ __('is_popular') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <img src="{{ $category->icon }}" alt="{{ $category->translation->title }}"
                                                    class="w-8 h-8 object-cover rounded">
                                            </td>
                                            <td>{{ $category->translation->title }}</td>
                                            <td>
                                                <div class="flex items-center justify-center">
                                                    <input type="checkbox" id="hs-small-switch-{{ $category->id }}"
                                                        class="ti-switch shrink-0 !w-11 !h-6 before:size-5"
                                                        data-item-id="{{ $category->id }}"
                                                        data-route="{{ route('categories.updateActivation') }}"
                                                        {{ $category->is_active ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="flex items-center justify-center">
                                                    <input type="checkbox" id="popular-switch-{{ $category->id }}"
                                                        class="ti-switch shrink-0 !w-11 !h-6 before:size-5"
                                                        data-item-id="{{ $category->id }}"
                                                        data-route="{{ route('categories.updatePopularStatus') }}"
                                                        {{ $category->is_popular ? 'checked' : '' }}>
                                                </div>
                                            </td>

                                            <td>
                                                <a aria-label="anchor"
                                                    href="{{ route('subCategories.index', ['category_id' => $category->id]) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-secondary mx-1 rounded-pill">
                                                    <i class="las la-layer-group"></i>
                                                </a>
                                                <a aria-label="anchor" href="{{ route('categories.show', $category->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-primary mx-1 rounded-pill">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                <a aria-label="anchor" href="{{ route('categories.edit', $category->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <a aria-label="anchor" href="javascript:void(0);"
                                                    onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $category->id }})"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                    <i class="las la-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $category->id }}"
                                                    action="{{ route('categories.destroy', $category->id) }}"
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
