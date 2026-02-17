@extends('layouts.master')
@section('title', __('document_content_details'))

@section('content')
    <div class="content">
        <div class="main-content">

            <!-- Page Header -->
            <div class="block justify-between page-header md:flex items-center mb-4 p-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-alt text-primary"></i> {{ __('document_content_details') }}
                </h3>

                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('document-contents.index') }}">
                            {{ __('documents') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>

                    <li class="text-[0.813rem] font-semibold">
                        {{ __('document_content_details') }}
                    </li>
                </ol>
            </div>

            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box bg-white p-6 shadow rounded-lg">

                        <div class="box-header border-b pb-4 mb-4">
                            <h5 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                {{ __('document_content_details') }}
                            </h5>
                        </div>

                        <div class="box-body">
                            <div class="grid grid-cols-12 gap-6">

                                {{-- Category --}}
                                <div class="col-span-12 md:col-span-6">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            <i class="fas fa-folder text-yellow-500 mr-1"></i>
                                            {{ __('sub_category') }}
                                        </label>

                                        <p class="text-gray-800 font-semibold">
                                            {{ $document->category->translation?->name }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Parent Category (if exists) --}}
                                @if ($document->category->parent)
                                    <div class="col-span-12 md:col-span-6">
                                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                            <label class="block text-sm font-medium text-gray-600 mb-1">
                                                <i class="fas fa-sitemap text-purple-500 mr-1"></i>
                                                {{ __('category') }}
                                            </label>

                                            <p class="text-gray-800 font-semibold">
                                                {{ $document->category->parent->translation?->name }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Content EN --}}
                                <div class="col-span-12">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-3">
                                            <i class="fas fa-language text-blue-500 mr-1"></i>
                                            {{ __('content_en') }}
                                        </label>

                                        <div class="prose max-w-none text-gray-800">
                                            {!! $document->translations->firstWhere('language', 'en')?->content !!}
                                        </div>
                                    </div>
                                </div>

                                {{-- Content AR --}}
                                <div class="col-span-12">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-3">
                                            <i class="fas fa-language text-green-500 mr-1"></i>
                                            {{ __('content_ar') }}
                                        </label>

                                        <div class="prose max-w-none text-gray-800 text-right">
                                            {!! $document->translations->firstWhere('language', 'ar')?->content !!}
                                        </div>
                                    </div>
                                </div>

                                {{-- Dates --}}
                                <div class="col-span-12 md:col-span-6">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            <i class="fas fa-calendar-alt text-gray-500 mr-1"></i>
                                            {{ __('created_at') }}
                                        </label>
                                        <p class="text-gray-800">
                                            {{ $document->created_at->format('Y-m-d H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-span-12 md:col-span-6">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            <i class="fas fa-calendar-check text-gray-500 mr-1"></i>
                                            {{ __('updated_at') }}
                                        </label>
                                        <p class="text-gray-800">
                                            {{ $document->updated_at->format('Y-m-d H:i') }}
                                        </p>
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
