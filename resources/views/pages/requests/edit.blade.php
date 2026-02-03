@extends('layouts.master')
@section('title', __('request_details'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('build/assets/datatable/custom.datatable.css') }}">
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('request_details') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('request_details') }}</li>
                </ol>
            </div>
        </div>
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('request_details') }}</h5>
                        </div>
                        <div class="box-body">
                            <div class="max-w-5xl mx-auto p-6 bg-white rounded-xl shadow-md space-y-6">
                                <h1 class="text-2xl font-bold text-gray-800">{{ __('request_details') }}:
                                    {{ $request->order_number }}</h1>

                                {{-- Order Info --}}
                                <div class="border p-4 rounded-md">
                                    <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ __('request_details') }}</h2>
                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-6">
                                            <div class="my-3"><strong>{{ __('title') }}:</strong> {{ $request->translation->title }}
                                            </div>
                                            <div class="my-3"><strong>{{ __('status') }}:</strong>
                                                {{ ucfirst($request->status) }}</div>
                                            <div class="my-3"><strong>{{ __('start_date') }}:</strong>
                                                {{ $request->start_date ?? 'N/A' }}</div>
                                            <div class="my-3"><strong>{{ __('end_date') }}:</strong>
                                                {{ $request->end_date }}</div>
                                            <div class="my-3">
                                                @if (isset($request->status) && $request->status !== '')
                                                <span class="{{ \App\Enums\RequestStatusEnum::from($request->status)->badge() }}">
                                                    {{ \App\Enums\RequestStatusEnum::from($request->status)->label() }}
                                                </span>
                                         @endif

                                            </div>
                                        </div>
                                        <div class="col-span-6"><strong>{{ __('image') }}:</strong><br>
                                            <img src="{{ asset($request->image) }}" alt="Service Image"
                                                class="mt-2 h-32 w-full object-cover rounded-md">
                                        </div>
                                    </div>
                                </div>

                                {{-- User Info --}}
                                <div class="border p-4 rounded-md">
                                    <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ __('user_information') }}</h2>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div><strong>{{ __('username') }}:</strong> {{ $request->user->username }}</div>
                                        <div><strong>{{ __('email') }}:</strong> {{ $request->user->email }}</div>
                                        <div><strong>{{ __('phone') }}:</strong> {{ $request->user->full_phone }}</div>
                                        <div><strong>{{ __('gender') }}:</strong> {{ $request->user->gender_label }}
                                        </div>
                                        <div><strong>{{ __('avatar') }}:</strong><br>
                                            <img src="{{ asset($request->user->avatar) }}" alt="Avatar"
                                                class="mt-2 h-24 w-24 rounded-full object-cover">
                                        </div>
                                        <div><strong>{{ __('languages') }}:</strong><br>
                                            @foreach ($request->user->languages as $lang)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 bg-gray-100 text-sm rounded-md">
                                                    <img src="{{ $lang->language->flag }}"
                                                        alt="{{ $lang->language->title }}" class="h-4 w-6 mr-1">
                                                    {{ $lang->language->title }} ({{ ucfirst($lang->level) }})
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Service Info --}}
                                <div class="border p-4 rounded-md">
                                    <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ __('service_information') }}
                                    </h2>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div><strong>{{ __('title') }}:</strong>
                                            {{ $request->service->translation->title }}</div>
                                        <div class="col-span-2"><strong>{{ __('description') }}:</strong>
                                            {{ $request->service->translation->description }}</div>
                                    </div>
                                </div>

                                {{-- Plan Info --}}
                                <div class="border p-4 rounded-md">
                                    <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ __('plan_information') }}</h2>
                                    <div><strong>{{ __('plan') }}:</strong> {{ $request->plan->translation->title }}
                                    </div>
                                    <div class="mt-3 space-y-2">
                                        {{-- @foreach ($request->plan->features as $feature) --}}
                                        @foreach ($request->features as $feature)
                                            <div class="flex justify-between bg-gray-200 px-4 py-2 rounded">
                                                <span>{{ $feature->translation->title }}</span>
                                                <span class="font-medium">{{ $feature->value }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Logs --}}
                                @if (count($request->logs) > 0)
                                    <div class="border p-4 rounded-md">
                                        <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ __('logs') }}</h2>
                                        <div class="mt-3 space-y-2">
                                            @foreach ($request->logs as $log)
                                                <div class="flex justify-start bg-gray-200 px-4 py-2 rounded">
                                                    <span>{{ $log->translation->action }}</span>
                                                </div>
                                            @endforeach
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
@push('scripts')
@endpush
