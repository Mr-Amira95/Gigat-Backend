@extends('layouts.master')
@section('title', __('create_role'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium">
                        {{ __('create_role') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap min-w-0">
                    <li class="text-sm">
                        <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate"
                            href="{{ route('roles.index') }}">
                            {{ __('roles') }}
                            <i
                                class="fas fa-angle-right flex-shrink-0 mx-3 overflow-visible dark:text-gray-600 dark:hover:text-primary"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                        {{ __('create_role') }}
                    </li>
                </ol>
            </div>
              @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Page Header Close -->
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-body">
                            <form action="{{ route('roles.store') }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12">
                                        <label for="name" class="block text-sm font-medium text-gray-700">
                                            {{ __('role_name') }}
                                        </label>
                                        <input type="text" name="name" id="name"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            value="{{ old('name') }}" required>
                                    </div>

                                    <div class="col-span-12">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('permissions') }}
                                        </label>
                                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                            @foreach ($permissions as $permission)
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="permissions[]"
                                                        id="permission_{{ $permission->id }}"
                                                        value="{{ $permission->name }}"
                                                        class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                                    <label for="permission_{{ $permission->id }}"
                                                        class="mx-2 text-sm text-gray-700">
                                                        {{ __($permission->name) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-center">
                                    <button type="submit"
                                        class="px-6 py-2 text-white bg-primary hover:bg-primary-700 rounded-md shadow-sm">
                                        <i class="las la-save"></i> {{ __('save') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\Admin\RoleRequest') !!}
@endpush
