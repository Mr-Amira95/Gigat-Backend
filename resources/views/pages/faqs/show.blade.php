@extends('layouts.master')
@section('title', __('faq_details'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex items-center mb-4 p-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-question-circle text-primary"></i> {{ __('faq_details') }}
                </h3>
                <ol class="flex items-center whitespace-nowrap text-sm text-gray-600">
                    <li class="pe-2">
                        <a class="flex items-center text-primary hover:underline" href="{{ route('faqs.index') }}">
                            <i class="fas fa-question-circle me-1"></i> {{ __('faqs') }}
                        </a>
                    </li>
                    <li class="px-2 text-gray-500">/</li>
                    <li class="font-semibold text-primary">{{ __('faq_details') }}</li>
                </ol>
            </div>

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box bg-white p-6 shadow rounded-lg">
                        <!-- Header with Back Button -->
                        <div class="box-header flex justify-between items-center border-b pb-4 mb-4">
                            <h5 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i> {{ __('faq_details') }}
                            </h5>
                            <a href="{{ route('faqs.index') }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                <i class="fas fa-arrow-left"></i> {{ __('back') }}
                            </a>
                        </div>

                        <div class="box-body">
                            <!-- General Information -->
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <h6 class="font-semibold text-lg text-gray-700 mb-4 flex items-center gap-2">
                                    <i class="fas fa-info text-blue-500"></i> {{ __('faq_information') }}
                                </h6>
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="bg-white p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-folder text-gray-400 mr-1"></i> {{ __('faq_category') }}
                                        </label>
                                        <p class="text-gray-800 font-semibold">
                                            {{ $faq->faqable ? $faq->faqable->translation->title : __('general') }}
                                        </p>
                                    </div>
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
                                                    <i class="fas fa-question text-gray-400 mr-1"></i> {{ __('english_question') }}
                                                </label>
                                                <p class="text-gray-800 font-semibold">
                                                    {{ $faq->translations->firstWhere('language', 'en')?->question }}
                                                </p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <i class="fas fa-comment text-gray-400 mr-1"></i> {{ __('english_answer') }}
                                                </label>
                                                <div class="text-gray-700 prose max-w-none">
                                                    {!! $faq->translations->firstWhere('language', 'en')?->answer !!}
                                                </div>
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
                                                    <i class="fas fa-question text-gray-400 mr-1"></i> {{ __('arabic_question') }}
                                                </label>
                                                <p class="text-gray-800 font-semibold">
                                                    {{ $faq->translations->firstWhere('language', 'ar')?->question }}
                                                </p>
                                            </div>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    <i class="fas fa-comment text-gray-400 mr-1"></i> {{ __('arabic_answer') }}
                                                </label>
                                                <div class="text-gray-700 prose max-w-none">
                                                    {!! $faq->translations->firstWhere('language', 'ar')?->answer !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Media Section -->
                                @if($faq->media)
                                    <div class="col-span-12">
                                        <div class="p-6 border rounded-lg bg-gray-50 shadow-sm">
                                            <h6 class="font-semibold text-lg text-gray-700 mb-4 flex items-center gap-2">
                                                <i class="fas fa-photo-video text-purple-500"></i> {{ __('faq_media') }}
                                            </h6>
                                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                                @if(Str::endsWith($faq->media, ['.jpg', '.jpeg', '.png', '.gif']))
                                                    <img src="{{ asset($faq->media) }}" 
                                                         alt="FAQ Media" 
                                                         class="max-w-full h-auto rounded-lg">
                                                @elseif(Str::endsWith($faq->media, ['.mp4', '.webm', '.ogg']))
                                                    <video controls class="w-full rounded-lg">
                                                        <source src="{{ asset($faq->media) }}" type="video/mp4">
                                                        {{ __('no_media') }}
                                                    </video>
                                                @else
                                                    <a href="{{ asset($faq->media) }}" 
                                                       target="_blank"
                                                       class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
                                                        <i class="fas fa-external-link-alt mr-2"></i>
                                                        {{ __('view_media') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 