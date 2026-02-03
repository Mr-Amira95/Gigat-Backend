@extends('layouts.master')
@section('title', __('roles'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('roles') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('roles') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('roles') }}</h5>
                            <a href="{{ route('roles.create') }}"
                                class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                <i class="las la-plus-circle text-lg"></i>{{ __('create_role') }}
                            </a>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="white-space:nowrap">{{ __('role_name') }}</th>
                                        <th>{{ __('permissions') }}</th>
                                        <th>{{ __('actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ Str::title(str_replace('_', ' ', $role->name)) }}</td>
                                            <td>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($role->permissions as $permission)
                                                        <span
                                                            class="px-2 py-1 bg-gray-300 text-gray-700 rounded-full text-xs">
                                                            {{ __($permission->name) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('roles.edit', $role->id) }}"
                                                    class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-success mx-1 rounded-pill">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                @if ($role->name !== 'super_admin')
                                                    <a href="javascript:void(0);"
                                                        onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $role->id }})"
                                                        class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-danger mx-1 rounded-pill">
                                                        <i class="las la-trash"></i>
                                                    </a>
                                                    <form id="delete-form-{{ $role->id }}"
                                                        action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                        class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
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
@endpush
