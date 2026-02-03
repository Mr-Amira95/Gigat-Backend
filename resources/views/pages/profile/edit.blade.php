@extends('layouts.master')
@section('title', __('edit_profile'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3
                        class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 dark:text-white dark:hover:text-white text-[1.125rem] font-semibold">
                        {{ __('edit_profile') }}
                    </h3>
                </div>
                <ol class="flex items-center whitespace-nowrap min-w-0">
                    <li class="text-[0.813rem] text-defaulttextcolor font-semibold hover:text-primary dark:text-[#8c9097] dark:text-white/50"
                        aria-current="page">
                        {{ __('edit_profile') }}
                    </li>
                </ol>
            </div>
            <!-- Page Header Close -->
        </div>

        <div class="container">
                      <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box p-6 bg-white rounded-lg shadow">
                        <div class="box-header mb-4">
                            <h5 class="text-lg font-semibold text-gray-700">{{ __('edit_profile') }}</h5>
                        </div>

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-12 gap-6">
                                <!-- Username -->
                                <div class="col-span-12 md:col-span-6">
                                    <label for="username"
                                        class="block text-sm font-medium text-gray-700">{{ __('full_name') }}</label>
                                    <input type="text" name="username" id="username"
                                        value="{{ old('username', $admin->username) }}"
                                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                </div>

                                <!-- Email -->
                                <div class="col-span-12 md:col-span-6">
                                    <label for="email"
                                        class="block text-sm font-medium text-gray-700">{{ __('email') }}</label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $admin->email) }}"
                                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                </div>

                                <!-- Password -->
                                <div class="col-span-12 md:col-span-6">
                                    <label for="password"
                                        class="block text-sm font-medium text-gray-700">{{ __('password') }}</label>
                                    <input type="password" name="password" id="password"
                                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                    <p class="mt-1 text-sm text-gray-500">{{ __('leave_empty_to_keep_current_password') }}
                                    </p>
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-span-12 md:col-span-6">
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700">{{ __('confirm_password') }}</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-6 flex justify-center">
                                <button type="submit"
                                    class="px-6 py-2 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="las la-save"></i> {{ __('update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\Admin\ProfileRequest') !!}

    @endpush
