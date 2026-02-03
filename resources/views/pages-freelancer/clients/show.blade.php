@extends('layouts.master')
@section('title', __('client_details'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- عنوان الصفحة -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold flex items-center">
                        <i class="ti ti-user-circle me-2 text-primary"></i> {{ __('client_details') }}
                    </h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('client_details') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="text-center">
                                    <img src="{{ asset($client->avatar) ?? asset('build/assets/images/faces/default-avatar.jpg') }}"
                                        alt="{{ $client->username }}"
                                        class="mx-auto rounded-full w-32 h-32 object-cover mb-4 border-4 border-primary shadow-md" />
                                </div>

                                <div class="space-y-4 mb-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-user me-2"></i>{{ __('username') }}:</div>
                                        <div>{{ $client->username }}</div>

                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-mail me-2"></i>{{ __('email') }}:</div>
                                        <div>{{ $client->email }}</div>

                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-phone me-2"></i>{{ __('phone') }}:</div>
                                        <div>{{ $client->full_phone }}</div>

                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-gender-female me-2"></i>{{ __('gender') }}:</div>
                                        <div>{{ $client->gender_label }}</div>

                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-briefcase me-2"></i>{{ __('profession') }}:</div>
                                        <div>
                                            {{ $client->profession->translations->firstWhere('language', app()->getLocale())?->title }}
                                        </div>

                                        <div class="font-semibold flex items-center"><i
                                                class="ti ti-flag me-2"></i>{{ __('country') }}:</div>
                                        <div>
                                            <img src="{{ $client->country->flag }}" class="w-6 h-4 inline-block me-2">
                                            {{ $client->country->title }}
                                        </div>

                                      
                                        <div>
                                            <span
                                                class="text-white badge {{ $client->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $client->is_active ? __('active') : __('inactive') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="mt-4">
                                <h5 class="text-lg font-semibold mb-4 flex items-center">
                                    <i class="ti ti-id-badge me-2 text-primary"></i> {{ __('client_profile') }}
                                </h5>
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                           
                                         

                                            <div class="font-semibold flex items-center">
                                                <i class="ti ti-language me-2"></i>{{ __('languages') }}:
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($client->languages as $UserLanguage)
                                                    <div class="flex items-center bg-gray-100 px-3 py-2 rounded-md">
                                                        <img src="{{ $UserLanguage->language->flag }}"
                                                            alt="{{ $UserLanguage->language->title }}"
                                                            class="w-5 h-5 rounded-sm me-2">
                                                        <span
                                                            class="text-gray-700">{{ $UserLanguage->language->title }}</span>
                                                      
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- زر العودة -->
                            <div class="mt-6 text-center">
                                <a href="{{ route('clients.index') }}"
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
