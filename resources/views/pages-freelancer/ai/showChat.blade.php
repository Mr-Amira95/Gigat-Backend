@extends('layouts.master')
@section('title', __('chat'))
@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.15.0/echo.iife.js"></script>

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        #chatBody {
            overflow-y: auto;
            overflow-x: hidden;

            word-wrap: break-word;
        }

        @keyframes pulse {
                0% { opacity: 1; }
                50% { opacity: 0.4; }
                100% { opacity: 1; }
            }

        .recording {
            animation: pulse 1s infinite;
        }

        .custom-audio {
            width: 250px;
            max-width: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    {{-- <h3 class="text-[1.125rem] font-semibold">{{ __('chat_with') }} {{ $otherUser->username }}</h3> --}}
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
                            <h5 class="box-title"> {{ $otherUser->username }}</h5>
                            <a href="{{ route('freelancer.call.start', $otherUser->id) }}" class="btn btn-success">📞 Call {{ $otherUser->username }}</a>
                        </div>

                        <div id="chatBody" class="box-body overflow-y-auto flex-1 p-4 space-y-3 bg-gray-50"
                            style="height: 70vh;">
                            @foreach ($messages as $message)
                                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                    <div style="background:darkslateblue" class="max-w-xs p-3 rounded-lg {{ $message->sender_id === auth()->id() ? 'text-white' : 'bg-white border' }}">
                                        {{-- Attachment --}}
                                        @php
                                            $fileType = $message->attachment_type;
                                        @endphp
                                        @if ($message->attachment_url)
                                            @if (Str::startsWith($fileType, 'image'))
                                                <img src="{{ asset($message->attachment_url) }}"
                                                    class="mt-2 mb-2 rounded-lg max-w-[150px]">
                                            @elseif (Str::startsWith($fileType, 'video'))
                                                <video controls class="mt-2 mb-2 max-w-[150px]">
                                                    <source src="{{ asset($message->attachment_url) }}">
                                                </video>
                                            @elseif (Str::startsWith($fileType, 'audio'))
                                                <audio controls class="mt-2 mb-2">
                                                    <source src="{{ asset($message->attachment_url) }}">
                                                </audio>

                                            @else
                                                <a href="{{ asset($message->attachment_url) }}" target="_blank"
                                                    class="block mt-2 mb-2 underline">   {{ basename($message->attachment_url) }}
                                                    <br>
                                                    Download file</a>
                                            @endif
                                        @endif
                                        {{-- Text message --}}

                                        @if ($message->message && Str::startsWith($fileType, 'call'))
                                            <p class="text-sm text-white"> 📞 Call Duration <br> {{ $message->message }}</p>
                                        @else
                                            <p class="text-sm text-white">{{ $message->message }}</p>
                                        @endif

                                        {{-- Time --}}
                                        <span class="block text-xs text-gray-400 mt-1">{{ $message->created_at->format('H:i') }}</span>
                                    </div>
                                </div> @endforeach
                        </div>

                        <div class="p-4
        border-t bg-white">
    <form id="sendMessageForm" enctype="multipart/form-data">
        @csrf
        <div class="flex items-center gap-3">
            <input type="text" name="message" id="messageInput" placeholder="{{ __('message') }}"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">

            <input type="file" name="attachment" id="attachmentInput" class="hidden"
                accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx">

            <button type="button" id="startRecordingBtn" class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">
                <i class="fas fa-microphone"></i>
            </button>
            <span id="recordingTimer" class="text-sm text-gray-600 hidden"></span>

            <button type="button" onclick="document.getElementById('attachmentInput').click()"
                class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">
                <i class="fa fa-paperclip"></i>
            </button>

            <div id="attachmentPreview" class="flex items-center gap-2 mt-2"></div>
            <div id="audioPreview" class="mt-2"></div>


            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-700">
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
        const messages = {
            en: {
                fileWithAudio: "You can't send an attachment with a voice message. Please remove one."
            },
            ar: {
                fileWithAudio: "لا يمكنك إرسال مرفق مع رسالة صوتية. احذف واحد منهما."
            }
        };

        const currentLang = '{{ app()->getLocale() }}';
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.Pusher = Pusher;

            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: '{{ config('broadcasting.connections.pusher.key') }}',
                cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
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



            let mediaRecorder;
            let audioChunks = [];
            let recordingStartTime;
            let recordingInterval;

            const startRecordingBtn = document.getElementById('startRecordingBtn');
            const audioPreview = document.getElementById('audioPreview');
            const recordingTimer = document.getElementById('recordingTimer');

            startRecordingBtn.addEventListener('click', function() {
                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    // وقف التسجيل
                    mediaRecorder.stop();
                    clearInterval(recordingInterval);

                    startRecordingBtn.innerHTML = `<i class="fas fa-microphone"></i>`;
                    startRecordingBtn.classList.remove('bg-red-500', 'recording');
                    startRecordingBtn.classList.add('bg-gray-200');

                    recordingTimer.classList.add('hidden');
                    recordingTimer.textContent = '';
                } else {
                    // بدء التسجيل
                    navigator.mediaDevices.getUserMedia({
                            audio: true
                        })
                        .then(stream => {
                            mediaRecorder = new MediaRecorder(stream);
                            audioChunks = [];

                            mediaRecorder.start();
                            recordingStartTime = Date.now();
                            startRecordingBtn.innerHTML = `<i class="fas fa-stop"></i>`;
                            startRecordingBtn.classList.remove('bg-gray-200');
                            startRecordingBtn.classList.add('bg-red-500', 'recording');

                            // إظهار المؤقت
                            recordingTimer.classList.remove('hidden');
                            updateTimer(); // بدء أول مرة
                            recordingInterval = setInterval(updateTimer, 1000);

                            mediaRecorder.addEventListener('dataavailable', event => {
                                audioChunks.push(event.data);
                            });

                            mediaRecorder.addEventListener('stop', () => {
                                const audioBlob = new Blob(audioChunks, {
                                    type: 'audio/webm'
                                });
                                const audioUrl = URL.createObjectURL(audioBlob);

                                audioPreview.innerHTML = `
                                    <div class="flex items-center gap-2">
                                        <audio controls class="custom-audio">
                                            <source src="${audioUrl}" type="audio/webm">
                                        </audio>
                                        <button type="button" id="removeAudioBtn" class="text-red-500 text-lg">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                `;

                                document.getElementById('removeAudioBtn').onclick = function() {
                                    audioPreview.innerHTML = '';
                                    audioBlobToSend = null;
                                };

                                audioBlobToSend = audioBlob;
                            });
                        })
                        .catch(error => {
                            console.error('Microphone access denied.', error);
                            alert('Allow microphone access to record voice messages.');
                        });
                }
            });

            function updateTimer() {
                const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
                const minutes = String(Math.floor(elapsed / 60)).padStart(2, '0');
                const seconds = String(elapsed % 60).padStart(2, '0');
                recordingTimer.textContent = `${minutes}:${seconds}`;
            }

            let audioBlobToSend = null;




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


                    let content = `
                            <div style="background:darkslateblue" class="max-w-xs p-3 rounded-lg ${e.message.sender_id == {{ auth()->id() }} ? 'text-white' : 'bg-white border'}">
                        `;

                    // Attachment
                    if (e.message.attachment_url) {
                        const fileUrl = e.message.attachment_url;
                        const fileType = e.message.attachment_type;
                        const fileName = fileUrl.split('/').pop();

                        if (fileType.startsWith('image')) {
                            content += `<img src="${fileUrl}" class="mt-2 mb-2 rounded-lg max-w-[150px]">`;
                        } else if (fileType.startsWith('video')) {
                            content +=
                                `<video controls class="mt-2 mb-2 max-w-[150px]"><source src="${fileUrl}"></video>`;
                        } else if (fileType.startsWith('audio')) {
                            content += `<audio controls class="mt-2 mb-2"><source src="${fileUrl}"></audio>`;
                        } else {
                            content +=
                                `<a href="${fileUrl}" target="_blank" class="block mt-2 mb-2 underline">${fileName} <br> Download file</a>`;
                        }
                    }

                    // Message
                    const fileType = e.message.attachment_type;
                    if (e.message.message && fileType?.startsWith('call')) {
                        content +=
                            `<p class="text-sm text-white">📞 Call Duration <br> ${e.message.message}</p>`;
                    } else {
                        content += `<p class="text-sm text-white">${e.message.message}</p>`;
                    }

                    content += `
                        <span class="block text-xs text-gray-400 mt-1">
                            ${new Date(e.message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                        </span>
                    </div>`;
                    bubble.innerHTML = content;
                    chatBody.appendChild(bubble);
                    chatBody.scrollTop = chatBody.scrollHeight;
                });


            const attachmentInput = document.getElementById('attachmentInput');
            const attachmentPreview = document.getElementById('attachmentPreview');

            attachmentInput.addEventListener('change', function() {
                attachmentPreview.innerHTML = ''; // Clear old preview if any

                if (attachmentInput.files.length > 0) {
                    const file = attachmentInput.files[0];
                    const fileName = file.name;

                    const fileIcon = document.createElement('span');
                    fileIcon.innerHTML = `<i class="fas fa-paperclip text-gray-600"></i>`;

                    const fileLabel = document.createElement('span');
                    fileLabel.textContent = fileName;
                    fileLabel.classList.add('text-sm', 'text-gray-700');

                    const removeBtn = document.createElement('button');
                    removeBtn.innerHTML = `<i class="fas fa-times text-red-500"></i>`;
                    removeBtn.type = 'button';
                    removeBtn.classList.add('ml-2');
                    removeBtn.onclick = function() {
                        attachmentInput.value = '';
                        attachmentPreview.innerHTML = '';
                    }

                    attachmentPreview.appendChild(fileIcon);
                    attachmentPreview.appendChild(fileLabel);
                    attachmentPreview.appendChild(removeBtn);
                }
            });




            // Handle sending new message via AJAX
            const sendMessageForm = document.getElementById('sendMessageForm');
            console.log('sendMessageForm:', sendMessageForm);

            if (sendMessageForm) {

                sendMessageForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const messageInput = document.getElementById('messageInput');
                    const attachmentInput = document.getElementById('attachmentInput');

                    if (audioBlobToSend && attachmentInput.files.length > 0) {
                        console.log('condition matched');
                        alert(messages[currentLang].fileWithAudio);
                        return;
                    }
                    if (
                        !audioBlobToSend &&
                        attachmentInput.files.length === 0 &&
                        messageInput.value.trim() === ''
                    ) {
                        alert(currentLang === 'ar' ?
                            'يجب إدخال رسالة نصية أو مرفق أو تسجيل صوتي' :
                            'Please enter a message, attachment, or voice recording');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('message', messageInput.value);
                    if (attachmentInput.files.length > 0) {
                        formData.append('attachment', attachmentInput.files[0]);
                    }

                    if (audioBlobToSend) {
                        formData.append('audio', audioBlobToSend, 'voice_message.webm');
                    }

                    axios.post("{{ route('freelancer.chat.sendMessage', $chat->id) }}", formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        })
                        .then(response => {
                            console.log('Message sent successfully');

                            messageInput.value = '';
                            attachmentInput.value = '';
                            attachmentPreview.innerHTML = '';
                            audioPreview.innerHTML = '';
                            audioBlobToSend = null;
                            clearInterval(recordingInterval);
                            recordingTimer.classList.add('hidden');
                            recordingTimer.textContent = '';


                            const chatBody = document.getElementById('chatBody');
                            if (!chatBody) return;

                            const bubble = document.createElement('div');
                            bubble.classList.add('flex', 'justify-end');

                            let content = `
                                    <div style="background:darkslateblue" class="max-w-xs p-3 rounded-lg text-white">
                                `;


                            if (response.data.message.attachment_url) {
                                const fileUrl = response.data.message.attachment_url;
                                const fileType = response.data.message.attachment_type;
                                const fileName = fileUrl.split('/').pop();


                                if (fileType.startsWith('image')) {
                                    content +=
                                        `<img src="${fileUrl}" class="mt-2 mb-2 rounded-lg max-w-[150px]">`;
                                } else if (fileType.startsWith('video')) {
                                    content +=
                                        `<video controls class="mt-2 mb-2 max-w-[150px]"><source src="${fileUrl}"></video>`;
                                } else if (fileType.startsWith('audio')) {
                                    content +=
                                        `<audio controls class="mt-2 mb-2"><source src="${fileUrl}"></audio>`;
                                } else {
                                    content +=
                                        `<a href="${fileUrl}" target="_blank" class="block mt-2 mb-2 underline">${fileName} <br> Download file</a>`;
                                }



                            }
                            const fileType = response.data.message.attachment_type;

                            if (response.data.message.message && fileType?.startsWith('call')) {
                                content +=
                                    `<p class="text-sm text-white">📞 Call Duration <br> ${response.data.message.message}</p>`;
                            } else {
                                content +=
                                    `<p class="text-sm text-white">${response.data.message.message}</p>`;
                            }
                            content += `
                                <span class="block text-xs text-gray-300 mt-1">
                                    ${response.data.message.created_at.substring(11, 16)}
                                </span>
                            </div>`;

                            bubble.innerHTML = content;
                            chatBody.appendChild(bubble);
                            chatBody.scrollTop = chatBody.scrollHeight;

                        })
                        .catch(error => {
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
