@extends('layouts.master')
@section('title', __('AI Chat'))

@section('content')
    <div class="content">
        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box h-[80vh] flex flex-col">

                        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-2xl font-semibold text-gray-800">{{ __('AI Chat') }}</h3>
                            <div class="flex items-center space-x-2">
                                {{-- Delete Button --}}
                                <form method="POST" action="{{ route('freelancer.ai.delete') }}"
                                    onsubmit="return confirm('Are you sure you want to delete all messages?')">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ $type }}">
                                    <button type="submit"
                                        class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition">
                                        Delete All
                                    </button>
                                </form>
                            </div>


                            {{-- <form method="GET" action="{{ route('freelancer.ai.index') }}">
                        <select name="type" onchange="this.form.submit()" class="form-select">
                            <option value="faq" {{ $type == 'faq' ? 'selected' : '' }}>FAQ</option>
                            <option value="service" {{ $type == 'service' ? 'selected' : '' }}>Service</option>
                        </select>
                    </form> --}}
                        </div>

                        {{-- Flash Messages --}}
                        @if (session('success'))
                            <div class="bg-green-100 text-green-800 p-3">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="bg-red-100 text-red-800 p-3">{{ session('error') }}</div>
                        @endif

                        {{-- Chat Messages --}}
                        <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
                            @forelse ($messages as $message)
                                <div class="flex {{ $message->role == 'user' ? 'justify-end' : 'justify-start' }}">
                                    <div
                                        class="max-w-lg p-4 rounded-lg
                                {{ $message->role == 'user' ? 'bg-blue-600 text-white' : 'bg-white text-gray-800 border' }}">
                                        {!! $message->message !!}
                                        <div class="text-xs mt-2 opacity-60 text-right">
                                            {{ $message->created_at->format('Y-m-d H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-gray-500 text-center">No messages found.</div>
                            @endforelse

                            {{-- scroll target --}}
                            <div id="chat-end"></div>
                        </div>

                        {{-- Send Message Form --}}
                        <div class="p-4 border-t border-gray-200 flex items-center space-x-4">
                            <form method="POST" action="{{ route('freelancer.ai.send') }}" id="chat-form"
                                class="flex w-full space-x-2">
                                @csrf
                                <input type="hidden" name="type" value="faq">
                                <textarea name="message" id="message-input" class="form-control flex-1" rows="2" required
                                    placeholder="Type your message..."></textarea>
                                <button type="submit" id="send-btn" class="btn btn-primary">Send</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var chatEnd = document.getElementById("chat-end");
            if (chatEnd) {
                chatEnd.scrollIntoView({
                    behavior: "smooth"
                });
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var chatEnd = document.getElementById("chat-end");
            if (chatEnd) {
                chatEnd.scrollIntoView({
                    behavior: "auto"
                });
            }

            const form = document.getElementById("chat-form");
            const sendBtn = document.getElementById("send-btn");
            const messageInput = document.getElementById("message-input");
            const chatBox = document.querySelector(".flex-1.overflow-y-auto");

            form.addEventListener("submit", function(e) {
                e.preventDefault(); // 🚫 منع إعادة تحميل الصفحة

                const message = messageInput.value.trim();
                if (message === "") return;
                messageInput.value = "";
                
                sendBtn.disabled = true;
                sendBtn.innerHTML = "Answering...";

                // أضف رسالة المستخدم مباشرة
                chatBox.innerHTML += `
                <div class="flex justify-end">
                    <div class="max-w-lg p-4 rounded-lg bg-blue-600 text-white">
                        ${message}
                        <div class="text-xs mt-2 opacity-60 text-right">Now</div>
                    </div>
                </div>
            `;
                chatEnd.scrollIntoView({
                    behavior: "smooth"
                });

                // إرسال الطلب للسيرفر
                fetch(form.action, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            message: message,
                            type: "faq"
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // أضف رد الـ AI
                        chatBox.innerHTML += `
                    <div class="flex justify-start">
                        <div class="max-w-lg p-4 rounded-lg bg-white text-gray-800 border">
                            ${data.message}
                            <div class="text-xs mt-2 opacity-60 text-right">Now</div>
                        </div>
                    </div>
                `;
                        chatEnd.scrollIntoView({
                            behavior: "smooth"
                        });
                    })
                    .catch(error => {
                        alert("Something went wrong.");
                        console.error(error);
                    })
                    .finally(() => {
                        messageInput.value = "";
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = "Send";
                    });
            });
        });
    </script>
@endpush
