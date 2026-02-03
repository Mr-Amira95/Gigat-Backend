@extends('layouts.master')
@section('title', __('company_details'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold flex items-center">
                        <i class="ti ti-building me-2 text-primary"></i> {{ __('company_details') }}
                    </h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('company_details') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-body">
                            <!-- Company Main Info -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="text-center">
                                    <img src="{{ asset($company->logo ?? 'build/assets/images/faces/22.jpg') }}"
                                        alt="{{ $company->translation?->name }}"
                                        class="mx-auto rounded-md w-40 h-40 object-cover mb-4 border-4 border-primary shadow-md" />
                                </div>

                                <div class="space-y-4 mb-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-building me-2"></i>{{ __('company_name') }}:</div>
                                        <div>{{ $company->translation?->name }}</div>

                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-file-description me-2"></i>{{ __('description') }}:</div>
                                        <div>{{ $company->translation?->description ?? '-' }}</div>

                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-mail me-2"></i>{{ __('email') }}:</div>
                                        <div>{{ $company->contact_email }}</div>

                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-phone me-2"></i>{{ __('phone') }}:</div>
                                        <div>{{ $company->contact_phone_number }}</div>

                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-map-pin me-2"></i>{{ __('country_of_registration') }}:</div>
                                        <div>{{ $company->translation?->country_of_registration }}</div>

                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-id me-2"></i>{{ __('registration_number') }}:</div>
                                        <div>{{ $company->registration_number }}</div>

                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-world me-2"></i>{{ __('website_url') }}:</div>
                                        <div>
                                            @if ($company->website_url)
                                                <a href="{{ $company->website_url }}" target="_blank"
                                                    class="text-primary">{{ $company->website_url }}</a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Assigned Freelancer -->
                            <div class="mt-4">
                                <h5 class="text-lg font-semibold mb-4 flex items-center">
                                    <i class="ti ti-user me-2 text-primary"></i> {{ __('freelancer') }}
                                </h5>
                                @php
                                    $freelancer = $company->freelancers->first();
                                @endphp

                                @if ($freelancer)
                                    {{-- @dd($freelancer->user) --}}
                                    <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <img src="{{ asset($freelancer->user->avatar) ?? asset('build/assets/images/faces/22.jpg') }}"
                                            class="w-16 h-16 rounded-full object-cover border border-primary">
                                        <div>
                                            <p class="font-semibold text-lg">{{ $freelancer->user->username }}</p>
                                            <p class="text-sm text-gray-600">{{ $freelancer->user->email }}</p>
                                            <p class="text-sm text-gray-600">{{ $freelancer->user->full_phone }}</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-gray-500">{{ __('no_freelancers_assigned') }}</p>
                                @endif
                            </div>

                            <hr>

                            <!-- Social Links -->
                            @if ($company->socialLinks->isNotEmpty())
                                <div class="mt-6">
                                    <h5 class="text-lg font-semibold mb-4 flex items-center">
                                        <i class="ti ti-link me-2 text-primary"></i> {{ __('social_links') }}
                                    </h5>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        @foreach ($company->socialLinks as $link)
                                            <div
                                                class="flex items-center gap-3 p-3 bg-gray-50 rounded-md border hover:bg-gray-100 transition">
                                                @if ($link->icon)
                                                    <img src="{{ asset($link->icon) }}" alt="icon"
                                                        class="w-6 h-6 rounded-md object-cover">
                                                @endif
                                                <div>
                                                    <p class="font-medium">
                                                        {{ $link->translation?->title ?? '-' }}
                                                    </p>
                                                    @if ($link->url)
                                                        <a href="{{ $link->url }}" target="_blank"
                                                            class="text-primary text-sm break-all">
                                                            {{ $link->url }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Back Button -->
                            <div class="mt-6 text-center">
                                <a href="{{ route('companies.index') }}"
                                    class="gap-2 px-4 py-1 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                    <i class="ti ti-arrow-left me-1"></i> {{ __('back') }}
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
