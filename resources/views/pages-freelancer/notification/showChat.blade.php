@extends('layouts.master')
@section('title', __('chat'))

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.0/echo.iife.js"></script>

    <style>
        #chatBody {
            overflow-y: auto;
        }
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('chat_with') }} {{ $otherUser->name }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('freelancer.chat.index') }}">
                            {{ __('chats') }}
                            <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('chat') }}</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box h-[70vh] flex flex-col">
                        <div class="box-header">
                            <h5 class="box-title">{{ __('chat_with') }} {{ $otherUser->name }}</h5>
                        </div>

                        <div id="chatBody" class="box-body overflow-y-auto flex-1 p-4 space-y-3 bg-gray-50"
                            style="height: 70vh;">
                            @foreach ($messages as $message)
                                <div
                                    class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                    <div
                                        class="max-w-xs p-3 rounded-lg {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-white border' }}">
                                        <p class="text-sm">{{ $message->message }}</p>
                                        <span
                                            class="block text-xs text-gray-400 mt-1">{{ $message->created_at->format('H:i') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="p-4 border-t bg-white">
                            <form id="sendMessageForm">
                                @csrf
                                <div class="flex items-center gap-3">
                                    <input type="text" name="message" id="messageInput"
                                        placeholder="{{ __('type_message') }}"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                                        required>
                                    <button type="submit"
                                        class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-700">
                                        <i class="las la-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.Pusher = Pusher;

            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: '{{ env('VITE_PUSHER_APP_KEY') }}',
                cluster: '{{ env('VITE_PUSHER_APP_CLUSTER') }}',
                forceTLS: true,
                encrypted: true,
                // authEndpoint: '/broadcasting/auth',
                // auth: {
                //     headers: {
                //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                //     }
                // }
            });

            console.log('DOM ready');

            const chatId = {{ $chat->id }};
            console.log('Chat ID:', chatId);

            // Listen for new messages on private channel
            Echo.channel('chat.' + chatId)
                .listen('.message.sent', (e) => {
                    console.log('New message event received:', e);

                    const chatBody = document.getElementById('chatBody');
                    if (!chatBody) {
                        console.error('chatBody element not found');
                        return;
                    }

                    // Create message bubble element
                    const bubble = document.createElement('div');
                    bubble.classList.add('flex', e.message.sender_id == {{ auth()->id() }} ? 'justify-end' :
                        'justify-start');

                    const content = `
                        <div class="max-w-xs p-3 rounded-lg ${e.message.sender_id == {{ auth()->id() }} ? 'bg-primary text-white' : 'bg-white border'}">
                            <p class="text-sm">${e.message.message}</p>
                            <span class="block text-xs text-gray-400 mt-1">
                                ${new Date(e.message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                            </span>
                        </div>
                    `;

                    bubble.innerHTML = content;
                    chatBody.appendChild(bubble);
                    chatBody.scrollTop = chatBody.scrollHeight;
                });

            // Handle sending new message via AJAX
            const sendMessageForm = document.getElementById('sendMessageForm');
            console.log('sendMessageForm:', sendMessageForm);

            if (sendMessageForm) {
                sendMessageForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const messageInput = document.getElementById('messageInput');
                    const message = messageInput.value.trim();

                    if (message === '') {
                        console.log('Message is empty, aborting');
                        return;
                    }

                    axios.post("{{ route('freelancer.chat.sendMessage', $chat->id) }}", {
                        message: message
                    }).then(response => {
                        console.log('Message sent successfully');

                        messageInput.value = '';

                        const chatBody = document.getElementById('chatBody');
                        if (!chatBody) return;

                        const bubble = document.createElement('div');
                        bubble.classList.add('flex', 'justify-end');

                        const content = `
                    <div class="max-w-xs p-3 rounded-lg bg-primary text-white">
                        <p class="text-sm">${message}</p>
                        <span class="block text-xs text-gray-400 mt-1">
                            ${response.data.message.created_at.substring(11, 16)}
                        </span>
                    </div>
                `;

                        bubble.innerHTML = content;
                        chatBody.appendChild(bubble);
                        chatBody.scrollTop = chatBody.scrollHeight;

                    }).catch(error => {
                        console.error('Error sending message:', error);
                        alert('Failed to send message.');
                    });


                });
            }

            function scrollToBottom() {
                const chatBody = document.getElementById('chatBody');
                if (chatBody) {
                    chatBody.scrollTop = chatBody.scrollHeight;
                    console.log('Scroll to bottom done');
                }
            }

            window.addEventListener('load', scrollToBottom);
        });
    </script>
@endpush
