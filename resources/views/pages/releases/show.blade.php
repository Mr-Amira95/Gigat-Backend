@extends('layouts.master')
@section('title', __('release_details'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex items-center mb-4 p-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-code-branch text-primary"></i> {{ __('release_details') }}
                </h3>
                <ol class="flex items-center whitespace-nowrap text-sm text-gray-600">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('admin.releases.index') }}">
                            {{ __('releases') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="font-semibold text-primary">{{ __('release_details') }}</li>
                </ol>
            </div>

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box bg-white p-6 shadow rounded-lg">
                        <!-- Header with Back Button -->
                        <div class="box-header flex justify-between items-center border-b pb-4 mb-4">
                            <h5 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i> {{ __('release_details') }}
                            </h5>
                        </div>

                        <div class="box-body">
                            <div class="grid grid-cols-12 gap-6">
                                <!-- Versions -->
                                <div class="col-span-12 md:col-span-4">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            <i class="fas fa-android text-green-500 mr-1"></i> {{ __('android_version') }}
                                        </label>
                                        <p class="text-gray-800 font-semibold">{{ $release->android_version }}</p>
                                    </div>
                                </div>

                                <div class="col-span-12 md:col-span-4">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            <i class="fab fa-apple text-gray-700 mr-1"></i> {{ __('ios_version') }}
                                        </label>
                                        <p class="text-gray-800 font-semibold">{{ $release->ios_version }}</p>
                                    </div>
                                </div>

                                <div class="col-span-12 md:col-span-4">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            <i class="fas fa-globe text-blue-500 mr-1"></i> {{ __('web_version') }}
                                        </label>
                                        <p class="text-gray-800 font-semibold">{{ $release->web_version }}</p>
                                    </div>
                                </div>

                                <!-- Release Note -->
                                <div class="col-span-12">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            <i class="fas fa-sticky-note text-yellow-500 mr-1"></i>
                                            {{ __('release_note_en') }}
                                        </label>
                                        <div class="prose max-w-none text-gray-800">
                                            {!! $release->translations->firstWhere('language', 'en')?->release_note !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-12">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            <i class="fas fa-sticky-note text-yellow-500 mr-1"></i>
                                            {{ __('release_note_ar') }}
                                        </label>
                                        <div class="prose max-w-none text-gray-800">
                                            {!! $release->translations->firstWhere('language', 'ar')?->release_note !!}
                                        </div>
                                    </div>
                                </div>
                                <!-- Status & Required -->
                                <div class="col-span-12 md:col-span-6">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            <i class="fas fa-exclamation-circle text-red-500 mr-1"></i>
                                            {{ __('is_required') }}
                                        </label>
                                        <span
                                            class="inline-block px-3 py-1 text-xs font-medium rounded-full
            {{ $release->is_required ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $release->is_required ? __('yes') : __('no') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            <i class="fas fa-toggle-on text-blue-500 mr-1"></i> {{ __('status') }}
                                        </label>
                                        <span
                                            class="inline-block px-3 py-1 text-xs font-medium rounded-full
            {{ $release->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $release->is_active ? __('active') : __('inactive') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Dates -->
                                <div class="col-span-12 md:col-span-6">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            <i class="fas fa-calendar-alt text-gray-500 mr-1"></i> {{ __('created_at') }}
                                        </label>
                                        <p class="text-gray-800">{{ $release->created_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            <i class="fas fa-calendar-check text-gray-500 mr-1"></i> {{ __('updated_at') }}
                                        </label>
                                        <p class="text-gray-800">{{ $release->updated_at->format('Y-m-d H:i') }}</p>
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
