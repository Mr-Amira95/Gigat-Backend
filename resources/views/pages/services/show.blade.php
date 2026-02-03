@extends('layouts.master')

@section('title', __('service_details'))

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ $service->translation->title }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('services.index') }}">
                            {{ __('services') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('service_details') }}</li>
                </ol>
            </div>

            <div class="container mt-6">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 lg:col-span-8">
                        <div class="box">
                            <div class="box-body">
                                <!-- Cover Image -->
                                @if ($service->cover)
                                    <img src="{{ asset($service->cover) }}" alt="Cover Image"
                                        class="w-full rounded-lg mb-6">
                                @endif

                                <!-- Basic Details -->
                                <div class="mb-6">
                                    <h4 class="text-lg font-semibold mb-3">{{ __('basic_info') }}</h4>
                                    <div class="grid grid-cols-2 gap-4 text-[0.95rem]">
                                        <div><strong>{{ __('title') }}:</strong> {{ $service->translation->title }}</div>
                                        <div><strong>{{ __('category') }}:</strong>
                                            {{ $service->subCategory->category->translation->title }}</div>
                                        <div><strong>{{ __('sub_category') }}:</strong>
                                            {{ $service->subCategory->translation->title }}</div>
                                        <div><strong>{{ __('freelancer') }}:</strong> {{ $service->user->username }}</div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-6">
                                    <h4 class="text-lg font-semibold mb-3">{{ __('description') }}</h4>
                                    <p class="text-gray-700">{{ $service->translation->description }}</p>
                                </div>

                                <!-- Service Plans -->
                                <div class="mb-6">
                                    <h4 class="text-lg font-semibold mb-3">{{ __('plans') }}</h4>
                                    @foreach ($servicePlans as $plan)
                                        <div class="border p-4 rounded-lg mb-4 shadow-sm">
                                            <h5 class="text-base font-semibold mb-3">{{ $plan->plan->translation->title }}
                                            </h5>
                                            <ul class="list-inside text-[0.95rem] text-gray-700">
                                                @foreach ($plan->features as $feature)
                                                    <li>
                                                        <strong>{{ $feature->translation->title }}:</strong>
                                                        @if ($feature->type === 'source_files')
                                                            {{ $feature->value ? __('with_source_files') : __('without_source_files') }}
                                                        @else
                                                            {{ $feature->value }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Tags -->
                                @if ($service->tags->count())
                                    <div class="mb-6">
                                        <h4 class="text-lg font-semibold mb-3">{{ __('tags') }}</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($service->tags as $tag)
                                                <span
                                                    class="px-3 py-1 bg-primary text-white text-sm rounded-full">{{ $tag->translation->title }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Thumbnails -->
                                @if ($service->media->count())
                                    <div class="mb-6">
                                        <h4 class="text-lg font-semibold mb-3">{{ __('thumbnails_images') }}</h4>
                                        <div class="grid grid-cols-3 gap-3">
                                            @foreach ($service->media as $media)
                                                <img src="{{ asset($media->media_path) }}" alt="Thumbnail"
                                                    class="rounded-lg shadow-sm">
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- Back Button -->
                        <div class="mt-6 text-center">
                            <a href="{{ route('services.index') }}"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                                <i class="ti ti-arrow-left"></i> {{ __('back') }}
                            </a>
                        </div>
                    </div>

                    <!-- Sidebar Summary -->
                    <div class="col-span-12 lg:col-span-4">
                        <div class="box">
                            <div class="box-body">
                                <h4 class="text-lg font-semibold mb-4">{{ __('service_summary') }}</h4>
                                <div class="text-[0.95rem] text-gray-700 space-y-2">
                                    <div><strong>{{ __('created_at') }}:</strong>
                                        {{ $service->created_at->format('Y-m-d') }}</div>
                                    <div><strong>{{ __('updated_at') }}:</strong>
                                        {{ $service->updated_at->format('Y-m-d') }}</div>
                                    <div><strong>{{ __('status') }}:</strong>
                                        <span
                                            class="inline-block px-2 py-1 text-xs rounded-full {{ $service->status == 'active' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                            {{ __($service->status) }}
                                        </span>
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
