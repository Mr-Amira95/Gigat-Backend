@extends('layouts.master')
@section('title', __('releases'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('releases') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('releases') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between items-center">
                            <h5 class="box-title">{{ __('releases') }}</h5>
                            @can('create_releases')
                                <a href="{{ route('admin.releases.create') }}"
                                    class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-plus-circle text-lg"></i>{{ __('add_release') }}
                                </a>
                            @endcan
                        </div>
                        <div class="box-body">
                            <table id="basic-table" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('android_version') }}</th>
                                        <th>{{ __('ios_version') }}</th>
                                        <th>{{ __('web_version') }}</th>
                                        <th>{{ __('is_required') }}</th>
                                        <th>{{ __('status') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($releases as $release)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $release->android_version }}</td>
                                            <td>{{ $release->ios_version }}</td>
                                            <td>{{ $release->web_version }}</td>
                                            <td>
                                                @php
                                                    $classes = $release->is_required
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800';
                                                    $label = $release->is_required ? __('yes') : __('no');
                                                @endphp

                                                <span
                                                    class="inline-block px-3 py-1 text-xs font-medium rounded-full {{ $classes }}">
                                                    {{ $label }}
                                                </span>
                                            </td>

                                            <td>
                                                @can('activate_releases')
                                                    <div class="flex items-center justify-center">
                                                        <input type="checkbox"
                                                            class="ti-switch shrink-0 !w-11 !h-6 before:size-5 toggle-activation"
                                                            data-item-id="{{ $release->id }}"
                                                            data-route="{{ route('admin.releases.updateActivation', $release->id) }}"
                                                            {{ $release->is_active ? 'checked' : '' }}>
                                                    </div>
                                                @else
                                                    <div class="flex items-center justify-center">
                                                        <input type="checkbox" disabled
                                                            class="ti-switch shrink-0 !w-11 !h-6 before:size-5"
                                                            {{ $release->is_active ? 'checked' : '' }}>
                                                    </div>
                                                @endcan
                                            </td>

                                            <td>
                                                @can('view_releases')
                                                    <a href="{{ route('admin.releases.show', $release->id) }}"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-primary mx-1 rounded-pill">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                @endcan

                                                @can('edit_releases')
                                                    <a href="{{ route('admin.releases.edit', $release->id) }}"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete_releases')
                                                    <a href="javascript:void(0);"
                                                        onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $release->id }})"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                        <i class="las la-trash"></i>
                                                    </a>
                                                    <form id="delete-form-{{ $release->id }}"
                                                        action="{{ route('admin.releases.destroy', $release->id) }}"
                                                        method="POST" class="hidden">
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
