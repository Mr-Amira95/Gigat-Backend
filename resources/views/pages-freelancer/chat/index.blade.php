@extends('layouts.master')
@section('title', __('Chats'))

@section('content')
<div class="content">
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12">
            <div class="box h-[80vh] flex flex-col">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-2xl font-semibold text-gray-800">{{ __('chats') }}</h3>
                </div>

                <div class="flex-1 overflow-y-auto p-4 bg-gray-50 space-y-3">
                    @forelse ($chats as $chat)
                        @php
                            $otherUser = $chat->user_id_one == auth()->id() ? $chat->userTwo : $chat->userOne;
                            $lastMessage = $chat->lastMessage;
                        @endphp

                        <a href="{{ route('freelancer.chat.show', $chat->id) }}"
                            class="flex items-center p-4 bg-white rounded-lg shadow hover:bg-gray-100 transition duration-200">
                            <img src="{{ $otherUser->avatar ? asset($otherUser->avatar) : asset('default-avatar.png') }}"
                                alt="Avatar" class="w-12 h-12 rounded-full object-cover me-4">

                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <h6 class="text-lg font-medium text-gray-800">{{ $otherUser->username }}</h6>
                                    @if ($lastMessage)
                                        <span class="text-sm text-gray-400">{{ $lastMessage->created_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $lastMessage ? \Illuminate\Support\Str::limit($lastMessage->message, 60) : __('No messages yet') }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <p class="text-center text-gray-500">{{ __('No chats found.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
