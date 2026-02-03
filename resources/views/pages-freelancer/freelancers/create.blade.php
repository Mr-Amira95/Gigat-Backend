@extends('layouts.master')
@section('title', __('create_freelancer'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('create_freelancer') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap min-w-0">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary hover:text-primary" href="{{ route('freelancers.index') }}">
                            {{ __('freelancers') }}
                            <i class="ti ti-chevrons-right px-[0.5rem]"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('create_freelancer') }}</li>
                </ol>
            </div>
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
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box p-6 bg-white rounded-lg  border-gray-300 shadow">
                        <form action="{{ route('freelancers.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-12 gap-6">
                                <div class="col-span-12 md:col-span-6">
                                    <label for="username" class="block text-sm font-medium">{{ __('full_name') }}</label>
                                    <input type="text" name="username" id="username"
                                        class="mt-1 block w-full rounded-lg  border-gray-300">
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <label for="email" class="block text-sm font-medium">{{ __('email') }}</label>
                                    <input type="email" name="email" id="email"
                                        class="mt-1 block w-full rounded-lg  border-gray-300">
                                </div>


                                <div class="col-span-12 md:col-span-6">
                                    <label for="prefix" class="block text-sm font-medium mb-2">{{ __('prefix') }}</label>
                                    <select name="prefix" id="prefix"
                                        class="mt-1 block w-full rounded-lg  border-gray-300">
                                        @foreach ($countries as $country)
                                            <option value="{{ $country['phone_code'] }}">{{ $country['phone_code'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-12 md:col-span-6">
                                    <label for="phone" class="block text-sm font-medium">{{ __('phone') }}</label>
                                    <input type="text" name="phone" id="phone"
                                        class="mt-1 block w-full rounded-lg  border-gray-300">
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <label for="gender" class="block text-sm font-medium">{{ __('gender') }}</label>
                                    <select name="gender" id="gender"
                                        class="mt-1 block w-full rounded-lg  border-gray-300">
                                        <option value="male">{{ __('male') }}</option>
                                        <option value="female">{{ __('female') }}</option>
                                    </select>
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <label for="profession_id"
                                        class="block text-sm font-medium mb-2">{{ __('profession') }}</label>
                                    <select name="profession_id" id="profession_id"
                                        class="mt-1 block w-full rounded-lg  border-gray-300">
                                        <option value="" selected disabled>{{ __('select_profession') }}</option>
                                        @foreach ($professions as $profession)
                                            <option value="{{ $profession->id }}">{{ $profession->translation->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <label for="country_id"
                                        class="block text-sm font-medium mb-2">{{ __('country') }}</label>
                                    <select name="country_id" id="country_id"
                                        class="mt-1 block w-full rounded-lg  border-gray-300">
                                        @foreach ($countries as $country)
                                            <option value="{{ $country['id'] }}">{{ $country['title'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <label for="languages"
                                        class="block text-sm font-medium mb-2">{{ __('languages') }}</label>
                                    <select name="languages[]" id="languages"
                                        class="js-example-basic-multiple mt-1 block w-full rounded-lg  border-gray-300"
                                        multiple>

                                        @foreach ($languages as $language)
                                            <option value="{{ $language['id'] }}">{{ $language['title'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <label for="password" class="block text-sm font-medium">{{ __('password') }}</label>
                                    <input type="password" name="password" id="password"
                                        class="mt-1 block w-full rounded-lg  border-gray-300">
                                </div>


                                <div class="col-span-12 md:col-span-6">
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium">{{ __('confirm_password') }}</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="mt-1 block w-full rounded-lg  border-gray-300">
                                </div>
                                <div class="col-span-12 md:col-span-12">
                                    <label for="bio" class="block text-sm font-medium">{{ __('bio') }}</label>
                                    <textarea rows="3" name="bio" id="bio" class="mt-1 block w-full rounded-lg  border-gray-300"></textarea>
                                </div>
                                <div class="col-span-12 md:col-span-6">
                                    <label for="categories"
                                        class="block text-sm font-medium mb-2">{{ __('categories') }}</label>
                                    <select name="category_ids[]" id="categories"
                                        class="js-example-basic-multiple mt-1 block w-full rounded-lg border-gray-300"
                                        multiple>
                                        <option value="" selected disabled>{{ __('select_categories') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->translation->title }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <label for="avatar"
                                        class="block text-sm font-medium mb-2">{{ __('avatar') }}</label>
                                    <input type="file" name="avatar" id="avatar"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                </div>
                                <div class="col-span-12">
                                    <label class="block text-sm font-medium mb-2">{{ __('certificates') }}</label>
                                    <div id="certificate-wrapper" class="space-y-4">
                                        <div class="certificate-group grid grid-cols-12 gap-4">
                                            <div class="col-span-12 md:col-span-6">
                                                <input type="file" name="file[]"
                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                                            </div>
                                            <div class="col-span-12 md:col-span-6">
                                                <input type="text" name="description[]"
                                                    placeholder="{{ __('certificate_description') }}"
                                                    class="block w-full rounded-lg border-gray-300">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" id="add-certificate-btn"
                                        class="mt-2 px-4 py-1 bg-secondary text-sm rounded-sm text-white">{{ __('add_more_certificates') }}</button>
                                </div>

                            </div>

                            <div class="mt-6 flex justify-center">
                                <button type="submit"
                                    class="px-6 py-2 text-white bg-primary rounded-lg  border-gray-300 shadow">
                                    <i class="las la-save"></i> {{ __('save') }}
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
    {!! JsValidator::formRequest('App\\Http\\Requests\\Admin\\FreelancerRequest') !!}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                placeholder: "{{ __('select_options') }}",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
            $('#country_id').select2({
                placeholder: "{{ __('select_country') }}",
                allowClear: true,
                width: '100%',
                closeOnSelect: false
            });
            $('#add-certificate-btn').on('click', function(e) {
                e.preventDefault();

                const certificateGroup = `
                <div class="certificate-group grid grid-cols-12 gap-4 mt-2">
                    <div class="col-span-12 md:col-span-6">
                        <input type="file" name="file[]" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <input type="text" name="description[]" placeholder="{{ __('certificate_description') }}"
                            class="block w-full rounded-lg border-gray-300">
                    </div>
                </div>
            `;

                $('#certificate-wrapper').append(certificateGroup);
            });
        });
    </script>
@endpush
