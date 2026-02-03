@extends('layouts.master')
@section('title', __('create_company'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('create_company') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('companies.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('companies') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('create_company') }}</li>
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
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('company_details') }}</h5>
                        </div>

                        <div class="box-body">
                            <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="grid grid-cols-12 gap-4">
                                    <!-- Freelancer -->
                                    <div class="col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('freelancer') }}</label>
                                        <select name="user_id" id="user_id" class="form-control mt-1">
                                            <option value="" selected disabled>{{ __('select_options') }}</option>
                                            @foreach ($freelancers as $freelancer)
                                                <option value="{{ $freelancer->id }}">{{ $freelancer->username }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Company Name -->
                                    <div class="col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('company_name') }}</label>
                                        <input type="text" name="name" value="{{ old('name') }}"
                                            class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                                    </div>

                                    <!-- Email -->
                                    <div class="col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('email') }}</label>
                                        <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                                            class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">{{ __('phone') }}</label>
                                        <input type="text" name="contact_phone_number"
                                            value="{{ old('contact_phone_number') }}"
                                            class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                                    </div>

                                    <!-- Country -->
                                    <div class="col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('country_of_registration') }}</label>
                                        <input type="text" name="country_of_registration"
                                            value="{{ old('country_of_registration') }}"
                                            class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                                    </div>

                                    <!-- Registration Number -->
                                    <div class="col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('registration_number') }}</label>
                                        <input type="text" name="registration_number"
                                            value="{{ old('registration_number') }}"
                                            class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                                    </div>

                                    <!-- Website -->
                                    <div class="col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('website_url') }}</label>
                                        <input type="text" name="website_url" value="{{ old('website_url') }}"
                                            class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                                    </div>

                                    <!-- Logo -->
                                    <div class="col-span-6">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('company_logo') }}</label>
                                        <input type="file" name="logo" accept="image/*"
                                            class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                                    </div>

                                    <!-- Description -->
                                    <div class="col-span-12">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('company_description') }}</label>
                                        <textarea name="description" rows="3"
                                            class="form-input w-full border-gray-300 rounded-md focus:ring-primary focus:border-primary">{{ old('description') }}</textarea>
                                    </div>

                                    <!-- Social Links -->
                                    <div class="col-span-12 mt-4">
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('social_links') }}</label>
                                        <div id="social-links-wrapper">
                                            <div class="grid grid-cols-12 gap-4 mb-3 social-link-item">
                                                <div class="col-span-3">
                                                    <input type="file" name="social_links[0][icon]" accept="image/*"
                                                        class="form-input w-full border-gray-300 rounded-md">
                                                </div>
                                                <div class="col-span-4">
                                                    <input type="text" name="social_links[0][title]"
                                                        placeholder="{{ __('title') }}"
                                                        class="form-input w-full border-gray-300 rounded-md">
                                                </div>
                                                <div class="col-span-4">
                                                    <input type="url" name="social_links[0][url]" placeholder="https://"
                                                        class="form-input w-full border-gray-300 rounded-md">
                                                </div>
                                                <div class="col-span-1 flex justify-center items-center">
                                                    <button type="button"
                                                        class="remove-social bg-red-500 text-white px-3 py-1 rounded-md">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" id="add-social"
                                            class="mt-2 bg-primary text-white px-4 py-2 rounded-md">
                                            <i class="ti ti-plus"></i> {{ __('add_social_link') }}
                                        </button>
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
    {!! JsValidator::formRequest('App\Http\Requests\Admin\CompanyRequest') !!}

    <script>
        let socialIndex = 1;
        $('#add-social').on('click', function() {
            const newField = `
        <div class="grid grid-cols-12 gap-4 mb-3 social-link-item">
            <div class="col-span-3">
                <input type="file" name="social_links[${socialIndex}][icon]" accept="image/*"
                    class="form-input w-full border-gray-300 rounded-md">
            </div>
            <div class="col-span-4">
                <input type="text" name="social_links[${socialIndex}][title]" placeholder="{{ __('title') }}"
                    class="form-input w-full border-gray-300 rounded-md">
            </div>
            <div class="col-span-4">
                <input type="url" name="social_links[${socialIndex}][url]" placeholder="https://"
                    class="form-input w-full border-gray-300 rounded-md">
            </div>
            <div class="col-span-1 flex justify-center items-center">
                <button type="button" class="remove-social bg-red-500 text-white px-3 py-1 rounded-md">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        </div>`;
            $('#social-links-wrapper').append(newField);
            socialIndex++;
        });

        $(document).on('click', '.remove-social', function() {
            $(this).closest('.social-link-item').remove();
        });
    </script>
@endpush
