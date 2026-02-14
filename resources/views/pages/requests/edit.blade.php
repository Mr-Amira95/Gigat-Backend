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
                                            <div class="my-3"><strong>{{ __('title') }}:</strong>
                                                {{ $request->translation->title }}
                                            </div>
                                            <div class="my-3"><strong>{{ __('status') }}:</strong>
                                                {{ ucfirst($request->status) }}</div>
                                            <div class="my-3"><strong>{{ __('start_date') }}:</strong>
                                                {{ $request->start_date ?? 'N/A' }}</div>
                                            <div class="my-3"><strong>{{ __('end_date') }}:</strong>
                                                {{ $request->end_date }}</div>
                                            <div class="my-3">
                                                @if (isset($request->status) && $request->status !== '')
                                                    <span
                                                        class="{{ \App\Enums\RequestStatusEnum::from($request->status)->badge() }}">
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
                                {{-- @if (count($request->logs) > 0)
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
                                @endif --}}

                                {{-- Logs --}}
                                @if ($request->logs->count() > 0)
                                    <div class="border p-4 rounded-md">
                                        <h2 class="text-xl font-semibold text-gray-700 mb-4">
                                            {{ __('logs') }}
                                        </h2>

                                        <div class="space-y-4">
                                            @foreach ($request->logs->sortByDesc('created_at') as $log)
                                                <div class="bg-gray-100 p-4 rounded">

                                                    {{-- Action + Date --}}
                                                    <div class="flex justify-between items-center mb-3">
                                                        <div class="font-semibold text-gray-800">
                                                            {{ $log->translation->action }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $log->created_at->locale(app()->getLocale())->translatedFormat('d F Y - h:i A') }}
                                                        </div>
                                                    </div>

                                                    {{-- Attachments --}}
                                                    @if ($log->attachments->count() > 0)
                                                        <div class="space-y-2">
                                                            @foreach ($log->attachments as $attachment)
                                                                <div
                                                                    class="flex justify-between items-center bg-white p-3 rounded border">

                                                                    <div>
                                                                        <div class="text-sm font-medium">
                                                                            {{ basename($attachment->media_path) }}
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex gap-3">
                                                                        {{-- View --}}
                                                                        <a href="{{ asset($attachment->media_path) }}"
                                                                            target="_blank"
                                                                            class="text-blue-600 hover:underline text-sm">
                                                                            {{ __('view') }}
                                                                        </a>

                                                                        {{-- Download --}}
                                                                        <a href="{{ asset($attachment->media_path) }}"
                                                                            download
                                                                            class="px-3 py-1 bg-primary text-white text-xs rounded hover:bg-primary/80">
                                                                            {{ __('download') }}
                                                                        </a>
                                                                    </div>

                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif


                                {{-- Deliveries & Feedbacks Section --}}
                                <div class="border p-4 rounded-md">
                                    {{-- ================= DELIVERIES ================= --}}
                                    @if ($request->deliveries->count() > 0)
                                        <h3 class="text-lg font-bold text-primary mb-3">
                                            {{ __('deliveries') }}
                                        </h3>

                                        @foreach ($request->deliveries as $delivery)
                                            <div class="bg-gray-100 p-4 rounded mb-4">
                                                <div class="flex justify-between items-center mb-2">
                                                    <div class="font-semibold text-gray-800">
                                                        {{ $delivery->translation->message ?? '-' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $delivery->created_at->locale(app()->getLocale())->translatedFormat('d F Y - h:i A') }}
                                                    </div>
                                                </div>

                                                {{-- Attachments --}}
                                                @if ($delivery->attachments->count() > 0)
                                                    <div class="space-y-2">
                                                        @foreach ($delivery->attachments as $attachment)
                                                            <div
                                                                class="flex justify-between items-center bg-white p-3 rounded border">

                                                                <div>
                                                                    <div class="text-sm font-medium">
                                                                        {{ basename($attachment->attachment_path) }}
                                                                    </div>
                                                                    <div class="text-xs text-gray-500">
                                                                        {{ strtoupper($attachment->type ?? '') }}
                                                                    </div>
                                                                </div>

                                                                <div class="flex gap-3">
                                                                    {{-- View --}}
                                                                    <a href="{{ asset($attachment->attachment_path) }}"
                                                                        target="_blank"
                                                                        class="text-blue-600 hover:underline text-sm">
                                                                        {{ __('view') }}
                                                                    </a>

                                                                    {{-- Download --}}
                                                                    <a href="{{ asset($attachment->attachment_path) }}"
                                                                        download
                                                                        class="px-3 py-1 bg-primary text-white text-xs rounded hover:bg-primary/80">
                                                                        {{ __('download') }}
                                                                    </a>
                                                                </div>

                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                            </div>
                                        @endforeach
                                    @endif


                                    {{-- ================= FEEDBACK ================= --}}
                                    @if ($request->feedbacks->count() > 0)
                                        <h3 class="text-lg font-bold text-primary mb-3 mt-6">
                                            {{ __('feedback') }}
                                        </h3>

                                        @foreach ($request->feedbacks as $feedback)
                                            <div class="bg-gray-100 p-4 rounded mb-4">

                                                <div class="flex justify-between items-center mb-2">
                                                    <div class="font-semibold text-gray-800">
                                                        {{ $feedback->translation->message ?? '-' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $feedback->created_at->locale(app()->getLocale())->translatedFormat('d F Y - h:i A') }}
                                                    </div>
                                                </div>


                                                {{-- Attachments --}}
                                                @if ($feedback->attachments->count() > 0)
                                                    <div class="space-y-2">
                                                        @foreach ($feedback->attachments as $attachment)
                                                            <div
                                                                class="flex justify-between items-center bg-white p-3 rounded border">

                                                                <div>
                                                                    <div class="text-sm font-medium">
                                                                        {{ basename($attachment->attachment_path) }}
                                                                    </div>
                                                                    <div class="text-xs text-gray-500">
                                                                        {{ strtoupper($attachment->type ?? '') }}
                                                                    </div>
                                                                </div>

                                                                <div class="flex gap-3">
                                                                    {{-- View --}}
                                                                    <a href="{{ asset($attachment->attachment_path) }}"
                                                                        target="_blank"
                                                                        class="text-blue-600 hover:underline text-sm">
                                                                        {{ __('view') }}
                                                                    </a>

                                                                    {{-- Download --}}
                                                                    <a href="{{ asset($attachment->attachment_path) }}"
                                                                        download
                                                                        class="px-3 py-1 bg-primary text-white text-xs rounded hover:bg-primary/80">
                                                                        {{ __('download') }}
                                                                    </a>
                                                                </div>

                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                            </div>
                                        @endforeach
                                    @endif

                                </div>



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
