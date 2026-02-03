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
                        <a class="flex items-center text-primary" href="{{ route('freelancer.home.index') }}">
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
                            <h5 class="box-title">{{ __('Request Logs History') }}</h5>
                        </div>
                        <div class="box-body">
                            <div class="max-w-3xl mx-auto p-6">
                                {{-- <h1 class="text-2xl font-bold mb-4">Request Logs History</h1> --}}

                                <div class="space-y-6">
                                    @foreach ($request->logs as $log)
                                        <div class="flex items-start space-x-3">
                                            <!-- Timeline Dot -->

                                            <!-- Log Content -->
                                            <div class="flex-1 bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                                <div class="flex items-center justify-between">
                                                    <h3 class="text-lg font-semibold text-gray-800">
                                                        {{ $log->translation->action }}
                                                    </h3>
                                                    <span class="text-xs text-gray-500">
                                                        {{ $log->created_at->format('Y-m-d H:i') }}
                                                    </span>
                                                </div>

                                                @if ($log->attachments->count() > 0)
                                                    <div class="mt-3">
                                                        {{-- <h4 class="text-sm font-medium text-gray-700 mb-1">Attachments:</h4> --}}
                                                        <div class="flex flex-wrap gap-2 float-right">
                                                            @foreach ($log->attachments as $attachment)
                                                                <a href="{{ asset($attachment->media_path) }}"
                                                                    target="_blank"
                                                                    class="inline-flex items-center px-3 py-1 text-sm bg-indigo-100 text-indigo-700 rounded-full hover:bg-indigo-200 transition">
                                                                    Attachment
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>


                                <div class="mt-6">
                                    <a href="{{ route('freelancer.requests.index') }}"
                                        class="text-indigo-600 hover:underline">← Back to Requests</a>
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
