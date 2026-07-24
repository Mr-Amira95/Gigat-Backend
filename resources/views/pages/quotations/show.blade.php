@extends('layouts.master')
@section('title', __('quotation_details'))
@push('styles')
    <!-- You can include additional styles here -->
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('quotation_details') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('quotation_details') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-between items-center">
                            <h5 class="box-title">{{ __('quotation_details') }}</h5>
                            <div>
                                @can('delete_quotations')
                                    <a aria-label="anchor" href="javascript:void(0);"
                                        onclick="showDeleteConfirmation('{{ __('are_you_sure') }}', {{ $quotation->id }})"
                                        class="ti-btn btn-wave ti-btn-danger flex items-center gap-1">
                                        <i class="las la-trash"></i> {{ __('delete') }}
                                    </a>

                                    <form id="delete-form-{{ $quotation->id }}"
                                        action="{{ route('quotations.destroy', $quotation->id) }}" method="POST"
                                        class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endcan
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="max-w-5xl mx-auto p-6 bg-white rounded-xl shadow-md space-y-6">
                                <h1 class="text-2xl font-bold text-gray-800">{{ __('quotation_details') }}:
                                    {{ $quotation->translation->title }}</h1>

                                {{-- Quotation Info --}}
                                <div class="border p-4 rounded-md">
                                    <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ __('quotation_information') }}
                                    </h2>
                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-6">
                                            <div class="my-3"><strong>{{ __('title') }}:</strong>
                                                {{ $quotation->translation->title }}</div>
                                            <div class="my-3"><strong>{{ __('price') }}:</strong>
                                                {{ number_format($quotation->price, 2) }}</div>
                                            <div class="my-3"><strong>{{ __('delivery_days') }}:</strong>
                                                {{ $quotation->delivery_day }} {{ __('days') }}</div>
                                            <div class="my-3"><strong>{{ __('revisions') }}:</strong>
                                                {{ $quotation->revisions }}</div>
                                            <div class="my-3"><strong>{{ __('status') }}:</strong>
                                                {{ ucfirst($quotation->status) }}</div>
                                        </div>
                                        <div class="col-span-6"><strong>{{ __('description') }}:</strong><br>
                                            {{ $quotation->translation->description }}</div>
                                    </div>
                                </div>
                                {{-- Attachments --}}
                                <div class="border p-4 rounded-md">
                                    <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ __('attachments') }}</h2>

                                    @if ($quotation->attachments && $quotation->attachments->count() > 0)
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                            @foreach ($quotation->attachments as $attachment)
                                                <div
                                                    class="border rounded-md p-3 bg-gray-50 flex flex-col items-center text-center">
                                                    @php
                                                        $ext = pathinfo(
                                                            $attachment->attachment_url,
                                                            PATHINFO_EXTENSION,
                                                        );
                                                        $isImage = in_array(strtolower($ext), [
                                                            'jpg',
                                                            'jpeg',
                                                            'png',
                                                            'gif',
                                                            'webp',
                                                        ]);
                                                    @endphp

                                                    @if ($isImage)
                                                        <a href="{{ asset($attachment->attachment_url) }}" target="_blank">
                                                            <img src="{{ asset($attachment->attachment_url) }}"
                                                                alt="Attachment"
                                                                class="h-32 w-32 object-cover rounded-md shadow-sm mb-2">
                                                        </a>
                                                    @else
                                                        <a href="{{ asset($attachment->attachment_url) }}" target="_blank"
                                                            class="flex flex-col items-center">
                                                            <i class="fas fa-file-alt text-5xl text-blue-500 mb-2"></i>
                                                            <span class="text-sm text-gray-600 truncate w-28">
                                                                {{ basename($attachment->attachment_url) }}
                                                            </span>
                                                        </a>
                                                    @endif

                                                    <a href="{{ asset($attachment->attachment_url) }}" download
                                                        class="mt-2 text-xs text-primary underline">
                                                        {{ __('download') }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500">{{ __('no_attachments') }}</p>
                                    @endif
                                </div>
                                {{-- User Info --}}
                                <div class="border p-4 rounded-md">
                                    <h2 class="text-xl font-semibold text-gray-700 mb-4">{{ __('user_information') }}</h2>

                                    <div class="grid grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <div><strong>{{ __('username') }}:</strong> {{ $quotation->user->username }}
                                            </div>
                                            <div><strong>{{ __('email') }}:</strong> {{ $quotation->user->email }}</div>
                                            <div><strong>{{ __('phone') }}:</strong> {{ $quotation->user->full_phone }}
                                            </div>
                                            <div><strong>{{ __('profession') }}:</strong>
                                                {{ $quotation->user->profession->translation->title }}</div>
                                        </div>

                                        <div class="flex justify-center items-center">
                                            <div>
                                                <strong>{{ __('avatar') }}:</strong>
                                                <img src="{{ asset($quotation->user->avatar) }}" alt="Avatar"
                                                    class="mt-2 h-24 w-24 rounded-full object-cover">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Comments --}}
                                <div class="border p-4 rounded-md">
                                    <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ __('comments') }}</h2>
                                    <div class="space-y-3">
                                        @foreach ($quotation->quotationComments as $comment)
                                            <div class="bg-gray-200 p-4 rounded-md">
                                                <strong>{{ $comment->user->username }}:</strong>
                                                <p>{{ $comment->translation->comment }}</p>
                                                <span
                                                    class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                        @endforeach
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

@push('scripts')
    <!-- You can include additional scripts here -->
@endpush
