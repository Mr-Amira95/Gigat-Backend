@extends('layouts.master')
@section('title', __('create_admin'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3
                        class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 dark:text-white dark:hover:text-white text-[1.125rem] font-semibold">
                        {{ __('create_admin') }}
                    </h3>
                </div>
                <ol class="flex items-center whitespace-nowrap min-w-0">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary hover:text-primary dark:text-primary truncate"
                            href="{{ route('home.index') }}">
                            {{ __('administrators') }}
                            <i
                                class="ti ti-chevrons-right flex-shrink-0 text-[#8c9097] dark:text-white/50 px-[0.5rem] overflow-visible rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] text-defaulttextcolor font-semibold hover:text-primary dark:text-[#8c9097] dark:text-white/50 "
                        aria-current="page">
                        {{ __('create_admin') }}
                    </li>
                </ol>
            </div>
            <!-- Page Header Close -->
        </div>
        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Start::row-1 -->
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box p-6 bg-white rounded-lg shadow">
                        <div class="box-header mb-4">
                            <h5 class="text-lg font-semibold text-gray-700">{{ __('create_admin') }}</h5>
                        </div>
                        <form action="{{ route('admins.store') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-12 gap-6">
                                <div class="col-span-12 md:col-span-6">
                                    <label for="username"
                                        class="block text-sm font-medium text-gray-700">{{ __('full_name') }}</label>
                                    <input type="text" name="username" id="username"
                                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <label for="email"
                                        class="block text-sm font-medium text-gray-700">{{ __('email') }}</label>
                                    <input type="email" name="email" id="email"
                                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <label for="password"
                                        class="block text-sm font-medium text-gray-700">{{ __('password') }}</label>
                                    <input type="password" name="password" id="password"
                                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700">{{ __('confirm_password') }}</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <label for="role"
                                        class="block text-sm font-medium text-gray-700">{{ __('role') }}</label>
                                    <select name="role" id="role"
                                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                        <option value="">{{ __('select_role') }}</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"> {{ Str::title(Str::replace('_', ' ', $role->name)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <div class="mt-6 flex justify-center">
                                <button type="submit"
                                    class="px-6 py-2 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-save"></i>
                                    {{ __('save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End::row-1 -->
        </div>

    </div>
@endsection
@push('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\Admin\AdminRequest') !!}

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                placeholder: "اختر الخيارات",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
        });
    </script>
@endpush
