@extends('layouts.master')
@section('title', __('filters'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('filters') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('filters') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between align-center">
                            <h5 class="box-title">{{ __('filters') }}</h5>
                            <div class="actions flex items-center gap-4">
                                <select id="category-filter" class="px-2 py-2 border rounded-lg">
                                    <option value="">{{ __('all_categories') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->translation->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('filters.create') }}"
                                    class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-plus-circle text-lg"></i> {{ __('add_filter') }}
                                </a>
                            </div>
                        </div>
                        <div class="box-body">
                            <table id="basic-table" class="table text-center">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('title') }}</th>
                                        <th>{{ __('category') }}</th>
                                        <th>{{ __('type') }}</th>
                                        <th>{{ __('options_count') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($filters as $filter)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $filter->translation->title }}</td>
                                            <td>{{ $filter->category->translation->title }}</td>
                                            <td>{{ \App\Enums\FilterTypeEnum::from($filter->type)->label() }}</td>
                                            <td>{{ $filter->options->count() == 0 ? __('no_options') : $filter->options->count() }}
                                            </td>
                                            <td>
                                                <a href="{{ route('filters.edit', $filter->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <a href="javascript:void(0);"
                                                    onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $filter->id }})"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                    <i class="las la-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $filter->id }}"
                                                    action="{{ route('filters.destroy', $filter->id) }}" method="POST"
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
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
