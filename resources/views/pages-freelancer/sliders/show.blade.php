@extends('layouts.master')
@section('title', __('slider_details'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex items-center mb-4 p-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-images text-primary"></i> {{ __('slider_details') }}
                </h3>
                <ol class="flex items-center whitespace-nowrap text-sm text-gray-600">
                    <li class="pe-2">
                        <a class="flex items-center text-primary hover:underline" href="">
                            <i class="fas fa-images me-1"></i> {{ __('sliders') }}
                        </a>
                    </li>
                    <li class="px-2 text-gray-500">/</li>
                    <li class="font-semibold text-primary">{{ __('slider_details') }}</li>
                </ol>
            </div>

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box bg-white p-6 shadow rounded-lg">
                        <!-- Header with Back Button -->
                        <div class="box-header flex justify-between items-center border-b pb-4 mb-4">
                            <h5 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i> {{ __('slider_details') }}
                            </h5>
                           
                        </div>

                        <div class="box-body">
                            <!-- General Information -->
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <h6 class="font-semibold text-lg text-gray-700 mb-4 flex items-center gap-2">
                                    <i class="fas fa-cog text-blue-500"></i> {{ __('general_information') }}
                                </h6>
                                <div class="grid grid-cols-2 gap-4">
                                  
                                    @if($slider->button_action)
                                        <div class="bg-white p-4 rounded-lg shadow-sm col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-link text-gray-400 mr-1"></i> {{ __('button_action') }}
                                            </label>
                                            <p class="text-gray-800">{{ $slider->button_action }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-6">
                                <!-- English Content -->
                                <div class="col-span-12 md:col-span-6">
                                    <div class="p-6 border rounded-lg bg-gray-50 shadow-sm hover:shadow transition-shadow duration-200">
                                        <h6 class="font-semibold text-lg text-gray-700 mb-4 flex items-center gap-2">
                                            <i class="fas fa-language text-blue-500"></i> {{ __('english_content') }}
                                        </h6>
                                        <div class="space-y-6">
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <i class="fas fa-heading text-gray-400 mr-1"></i> {{ __('title') }}
                                                </label>
                                                <p class="text-gray-800 font-semibold">
                                                    {{ $slider->translations->firstWhere('language', 'en')?->title }}
                                                </p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <i class="fas fa-align-left text-gray-400 mr-1"></i> {{ __('description') }}
                                                </label>
                                                <p class="text-gray-700 whitespace-pre-line">
                                                    {{ $slider->translations->firstWhere('language', 'en')?->description }}
                                                </p>
                                            </div>
                                            @if($slider->translations->firstWhere('language', 'en')?->button_text)
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <i class="fas fa-button text-gray-400 mr-1"></i> {{ __('button_text') }}
                                                </label>
                                                <p class="text-gray-700">
                                                    {{ $slider->translations->firstWhere('language', 'en')?->button_text }}
                                                </p>
                                            </div>
                                            @endif
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <i class="fas fa-photo-video text-gray-400 mr-1"></i> {{ __('media') }}
                                                </label>
                                                @php
                                                    $enTranslation = $slider->translations->firstWhere('language', 'en');
                                                @endphp
                                                @if($enTranslation && $enTranslation->media_type === 'video')
                                                    <video controls class="w-full rounded-lg shadow-sm">
                                                        <source src="{{ asset($enTranslation->media_path) }}" type="video/mp4">
                                                    </video>
                                                @else
                                                    <img src="{{ asset($enTranslation?->media_path) }}"
                                                        alt="{{ $enTranslation?->title }}"
                                                        class="w-full h-48 object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Arabic Content -->
                                <div class="col-span-12 md:col-span-6">
                                    <div class="p-6 border rounded-lg bg-gray-50 shadow-sm hover:shadow transition-shadow duration-200">
                                        <h6 class="font-semibold text-lg text-gray-700 mb-4 flex items-center gap-2">
                                            <i class="fas fa-language text-green-500"></i> {{ __('arabic_content') }}
                                        </h6>
                                        <div class="space-y-6">
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <i class="fas fa-heading text-gray-400 mr-1"></i> {{ __('title') }}
                                                </label>
                                                <p class="text-gray-800 font-semibold">
                                                    {{ $slider->translations->firstWhere('language', 'ar')?->title }}
                                                </p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <i class="fas fa-align-left text-gray-400 mr-1"></i> {{ __('description') }}
                                                </label>
                                                <p class="text-gray-700 whitespace-pre-line">
                                                    {{ $slider->translations->firstWhere('language', 'ar')?->description }}
                                                </p>
                                            </div>
                                            @if($slider->translations->firstWhere('language', 'ar')?->button_text)
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <i class="fas fa-button text-gray-400 mr-1"></i> {{ __('button_text') }}
                                                </label>
                                                <p class="text-gray-700">
                                                    {{ $slider->translations->firstWhere('language', 'ar')?->button_text }}
                                                </p>
                                            </div>
                                            @endif
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <i class="fas fa-photo-video text-gray-400 mr-1"></i> {{ __('media') }}
                                                </label>
                                                @php
                                                    $arTranslation = $slider->translations->firstWhere('language', 'ar');
                                                @endphp
                                                @if($arTranslation && $arTranslation->media_type === 'video')
                                                    <video controls class="w-full rounded-lg shadow-sm">
                                                        <source src="{{ asset($arTranslation->media_path) }}" type="video/mp4">
                                                    </video>
                                                @else
                                                    <img src="{{ asset($arTranslation?->media_path) }}"
                                                        alt="{{ $arTranslation?->title }}"
                                                        class="w-full h-48 object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 