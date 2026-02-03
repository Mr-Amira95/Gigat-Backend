@extends('layouts.master')

@section('title', __('profile'))

@section('content')
    <div class="content">
        <div class="main-content">
            <div class="page-header mb-6 flex items-center justify-between">
                <h3 class="text-[1.25rem] font-semibold">{{ __('profile') }}</h3>
            </div>

            <div class="container">
                <!-- Tabs -->
                <div>
                    <ul class="flex border-b mb-6">
                        <li class="mr-2">
                            <button
                                class="tab-link px-4 py-2 rounded-t-lg text-sm font-medium border-b-2 border-transparent hover:text-primary"
                                onclick="openTab(event, 'accountInfo')">{{ __('account_information') }}</button>
                        </li>
                        <li class="mr-2">
                            <button
                                class="tab-link px-4 py-2 rounded-t-lg text-sm font-medium border-b-2 border-transparent hover:text-primary"
                                onclick="openTab(event, 'changePassword')">{{ __('change_password') }}</button>
                        </li>
                        @if ($user->freelancer->status == 'unverified')
                            <li>
                                <button
                                    class="tab-link px-4 py-2 rounded-t-lg text-sm font-medium border-b-2 border-transparent hover:text-primary"
                                    onclick="openTab(event, 'verifyAccount')">{{ __('verify_account') }}</button>
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- Account Information Tab -->
                <div id="accountInfo" class="tab-content">
                    <div class="box p-6">
                        <div class="max-w-sm mx-auto text-center">
                            <img src="{{ $user->avatar ? asset($user->avatar) : asset('default-avatar.png') }}"
                                alt="Avatar" class="w-28 h-28 shadow-lg object-cover mx-auto" style="border-radius: 50%;">
                            <h4 class="mt-4 text-2xl font-semibold text-gray-800">{{ $user->username }}</h4>
                            <p class="mt-1 text-gray-500">{{ $user->email }}</p>
                            <a href="{{ route('freelancer.profile.edit', $user->id) }}"
                                class="mt-5 inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition mx-auto">
                                <i class="ti ti-edit text-lg"></i>
                                {{ __('edit_profile') }}
                            </a>

                        </div>

                        <div class="mt-10 grid grid-cols-1 md:grid-cols-1 gap-4 text-[0.95rem]">
                            <div><strong>{{ __('phone') }}:</strong> {{ $user->full_phone ?? __('not_provided') }}</div>
                            <div>
                                <strong>{{ __('gender') }}:</strong>
                                {{ $user->gender?->label() ?? __('not_provided') }}
                            </div>

                            <div><strong>{{ __('bio') }}:</strong> {{ $user->freelancer->translation?->bio ?? __('not_provided') }}
                            </div>
                            <div><strong>{{ __('profession') }}:</strong>
                                {{ $user->profession->translations->firstWhere('language', app()->getLocale())?->title ?? __('not_provided') }}
                            </div>
                            <div><strong>{{ __('country') }}:</strong>
                                {{ app()->getLocale() === 'ar' ? $user->country->title_ar : $user->country->title_en ?? __('not_provided') }}
                            </div>

                            <div>
                                <strong>{{ __('categories') }}:</strong>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @forelse ($user->categories as $category)
                                        <div class="flex items-center bg-gray-100 px-3 py-2 rounded-md">
                                            <span class="text-gray-700">{{ $category->translation->title }}</span>
                                        </div>
                                    @empty
                                        <span class="text-gray-500">{{ __('no_categories_selected') }}</span>
                                    @endforelse
                                </div>
                            </div>


                            <div>
                                <strong>{{ __('languages') }}:</strong>
                                @if ($user->languages->count())
                                    @foreach ($user->languages as $userLanguage)
                                        <div class="flex items-center bg-gray-100 px-3 py-2 rounded-md  max-w-xs">
                                            <img src="{{ $userLanguage->language->flag }}"
                                                alt="{{ $userLanguage->language->title_en }}"
                                                class="w-5 h-5 rounded-sm mr-2">
                                            <span style="margin: 5px" class="text-gray-700">
                                                {{ app()->getLocale() === 'ar' ? $userLanguage->language->title_ar : $userLanguage->language->title_en }}
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    {{ __('not_provided') }}
                                @endif
                            </div>

                            <div>
                                <strong>{{ __('certificates') }}:</strong>
                                @if ($user->certificates->count())
                                    <ul class="list-inside mt-1">
                                        @foreach ($user->certificates as $cert)
                                            <li>
                                                <a href="{{ asset($cert->file_path) }}" target="_blank"
                                                    class="text-blue-600 hover:underline">
                                                    {{ $cert->translation->description }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ __('not_provided') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Change Password Tab -->
                <div id="changePassword" class="tab-content hidden">
                    <div class="box p-6">
                        <h4 class="text-lg font-semibold mb-4">{{ __('change_password') }}</h4>
                        <form action="{{ route('freelancer.profile.updatePassword') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium">{{ __('current_password') }}</label>
                                <input type="password" name="current_password"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium">{{ __('new_password') }}</label>
                                <input type="password" name="new_password"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">

                                <ul class="text-xs text-gray-500 mt-1 list-disc ms-5">
                                    <li>{{ __('at_least_8_chars') }}</li>
                                    <li>{{ __('at_least_one_uppercase') }}</li>
                                    <li>{{ __('at_least_one_lowercase') }}</li>
                                    <li>{{ __('at_least_one_special_char') }}</li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium">{{ __('confirm_password') }}</label>
                                <input type="password" name="new_password_confirmation"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            <div class="mt-6 text-center">
                                <button type="submit"
                                    class="gap-2 px-4 py-2 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="ti ti-check me-1"></i> {{ __('save_changes') }}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Verify Account Tab -->
                <div id="verifyAccount" class="tab-content hidden">
                    <div class="box p-6">
                        <h4 class="text-lg font-semibold mb-4">{{ __('verify_account') }}</h4>
                        @if ($user->is_verified)
                            <div class="text-green-600 font-medium">{{ __('account_verified') }}</div>
                        @else
                            <form action="{{ route('freelancer.profile.verify') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                @if ($user->freelancer->file)
                                    <div class="mb-4">
                                        <label
                                            class="block text-sm font-medium mb-2">{{ __('old_verification_document') }}</label>
                                        <a href="{{ asset($user->freelancer->file) }}" target="_blank"
                                            class="text-blue-600 underline">
                                            {{ __('view_document') }}
                                        </a>
                                    </div>
                                @endif

                                <div class="mb-4">
                                    <label
                                        class="block text-sm font-medium">{{ __('upload_verification_document') }}</label>
                                    <input type="file" name="verification_document" class="form-input w-full">
                                </div>

                                <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-700">
                                    {{ __('submit_verification') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {!! JsValidator::formRequest('App\Http\Requests\Freelancer\ChangePasswordRequest') !!}
    <script>
        function openTab(evt, tabName) {
            let i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.add("hidden");
            }
            tablinks = document.getElementsByClassName("tab-link");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("border-primary");
            }
            document.getElementById(tabName).classList.remove("hidden");
            evt.currentTarget.classList.add("border-primary");
        }
        document.querySelector(".tab-link").click();
    </script>
@endpush
