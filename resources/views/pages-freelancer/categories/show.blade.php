@extends('layouts.master')
@section('title', __('category_details'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex items-center mb-4 p-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-folder text-primary"></i> {{ __('category_details') }}
                </h3>
                <ol class="flex items-center whitespace-nowrap text-sm text-gray-600">
                    <li class="pe-2">
                        <a class="flex items-center text-primary hover:underline" href="{{ route('categories.index') }}">
                            <i class="fas fa-folders me-1"></i> {{ __('categories') }}
                        </a>
                    </li>
                    <li class="px-2 text-gray-500">/</li>
                    <li class="font-semibold text-primary">{{ __('category_details') }}</li>
                </ol>
            </div>

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box bg-white p-6 shadow rounded-lg">
                        <!-- Header with Back Button -->
                        <div class="box-header flex justify-between items-center border-b pb-4 mb-4">
                            <h5 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i> {{ __('category_details') }}
                            </h5>
                            <a href="{{ route('categories.index') }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                <i class="fas fa-arrow-left"></i> {{ __('back') }}
                            </a>
                        </div>

                        <div class="box-body">
                            <div class="grid grid-cols-12 gap-6">
                                <!-- Icon Section with Label -->
                                <div class="col-span-12 text-center mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('icon') }}</label>
                                    <div class="p-4 bg-gray-50 rounded-lg inline-block">
                                        <img src="{{ asset($category->icon) }}"
                                            alt="{{ $category->translations->firstWhere('language', app()->getLocale())?->title }}"
                                            class="w-32 h-32 object-contain mx-auto border border-gray-200 p-2 rounded-lg shadow-sm">
                                    </div>
                                </div>

                                <!-- English Content -->
                                <div class="col-span-12 md:col-span-6">
                                    <div
                                        class="p-6 border rounded-lg bg-gray-50 shadow-sm hover:shadow transition-shadow duration-200">
                                        <h6 class="font-semibold text-lg text-gray-700 mb-4 flex items-center gap-2">
                                            <i class="fas fa-language text-blue-500"></i> {{ __('english_content') }}
                                        </h6>
                                        <div class="space-y-6">
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <i class="fas fa-heading text-gray-400 mr-1"></i> {{ __('title') }}
                                                </label>
                                                <p class="text-gray-800 font-semibold">
                                                    {{ $category->translations->firstWhere('language', 'en')?->title }}
                                                </p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <i class="fas fa-align-left text-gray-400 mr-1"></i>
                                                    {{ __('description') }}
                                                </label>
                                                <p class="text-gray-700 whitespace-pre-line">
                                                    {{ $category->translations->firstWhere('language', 'en')?->description }}
                                                </p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <i class="fas fa-image text-gray-400 mr-1"></i> {{ __('cover') }}
                                                </label>
                                                <img src="{{ asset($category->translations->firstWhere('language', 'en')?->cover) }}"
                                                    alt="{{ $category->translations->firstWhere('language', 'en')?->title }}"
                                                    class="w-full h-48 object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Arabic Content -->
                                <div class="col-span-12 md:col-span-6">
                                    <div
                                        class="p-6 border rounded-lg bg-gray-50 shadow-sm hover:shadow transition-shadow duration-200">
                                        <h6 class="font-semibold text-lg text-gray-700 mb-4 flex items-center gap-2">
                                            <i class="fas fa-language text-green-500"></i> {{ __('arabic_content') }}
                                        </h6>
                                        <div class="space-y-6">
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <i class="fas fa-heading text-gray-400 mr-1"></i> {{ __('title') }}
                                                </label>
                                                <p class="text-gray-800 font-semibold">
                                                    {{ $category->translations->firstWhere('language', 'ar')?->title }}
                                                </p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <i class="fas fa-align-left text-gray-400 mr-1"></i>
                                                    {{ __('description') }}
                                                </label>
                                                <p class="text-gray-700 whitespace-pre-line">
                                                    {{ $category->translations->firstWhere('language', 'ar')?->description }}
                                                </p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <i class="fas fa-image text-gray-400 mr-1"></i> {{ __('cover') }}
                                                </label>
                                                <img src="{{ asset($category->translations->firstWhere('language', 'ar')?->cover) }}"
                                                    alt="{{ $category->translations->firstWhere('language', 'ar')?->title }}"
                                                    class="w-full h-48 object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
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
