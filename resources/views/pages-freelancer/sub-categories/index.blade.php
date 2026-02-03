@extends('layouts.master')
@section('title', __('sub_categories'))
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
                    <h3 class="text-[1.125rem] font-semibold">{{ __('sub_categories') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('sub_categories') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            @if (session('error_message'))
                @php
                    $errorMessage = session('error_message');
                    $messageParts = explode('|', $errorMessage);

                    $message = $messageParts[0];
                    $subCategoryId = $messageParts[1] ?? null;
                @endphp

                <div class="alert alert-danger flex justify-between items-center">
                    <span>{{ $message }}</span>
                    @if ($subCategoryId)
                        <a href="{{ route('tags.index', ['subcategory_id' => $subCategoryId]) }}"
                            class="flex items-center gap-2 px-4 py-3 text-white bg-warning hover:bg-blue-600 rounded-lg shadow">
                            <i class="las la-arrow-right"></i>
                        </a>
                    @endif
                </div>
            @endif
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between align-center">
                            <h5 class="box-title">{{ __('sub_categories') }}</h5>
                            <div class="actions flex items-center gap-4">
                                <select id="category-filter" class="px-2 py-2 border rounded-lg">
                                    <option value="">{{ __('all_categories') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request()->get('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->translation->title }}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('subCategories.create') }}"
                                    class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-plus-circle text-lg"></i> {{ __('add_sub_category') }}
                                </a>
                            </div>
                        </div>
                        <div class="box-body">
                            <table id="basic-table" class="table text-center">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('icon') }}</th>
                                        <th>{{ __('title') }}</th>
                                        <th>{{ __('category') }}</th>
                                        <th>{{ __('status') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subCategories as $subCategory)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <img src="{{ asset($subCategory->icon) }}"
                                                    alt="{{ $subCategory->translation->title }}"
                                                    class="w-8 h-8 object-cover rounded">
                                            </td>
                                            <td>{{ $subCategory->translation->title }}</td>
                                            <td>{{ $subCategory->category->translation->title }}</td>
                                            <td>
                                                <div class="flex items-center justify-center">
                                                    <input type="checkbox" id="hs-small-switch-{{ $subCategory->id }}"
                                                        class="ti-switch shrink-0 !w-11 !h-6 before:size-5"
                                                        data-item-id="{{ $subCategory->id }}"
                                                        data-route="{{ route('subCategories.updateActivation') }}"
                                                        {{ $subCategory->is_active ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>

                                                <a aria-label="anchor"
                                                    href="{{ route('subCategories.edit', $subCategory->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <a aria-label="anchor" href="javascript:void(0);"
                                                    onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $subCategory->id }})"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                    <i class="las la-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $subCategory->id }}"
                                                    action="{{ route('subCategories.destroy', $subCategory->id) }}"
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "{{ __('all_categories') }}",
                allowClear: true,
                width: '100%'
            });

            $('#basic-table').DataTable();

            $('#category-filter').on('change', function() {
                const categoryId = $(this).val();
                const currentUrl = new URL(window.location.href);

                if (categoryId) {
                    currentUrl.searchParams.set('category_id', categoryId);
                } else {
                    currentUrl.searchParams.delete('category_id');
                }

                window.location.href = currentUrl.toString();
            });
        });
    </script>
@endpush
