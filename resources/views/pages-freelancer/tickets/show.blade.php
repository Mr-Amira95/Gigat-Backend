@extends('layouts.master')
@section('title', __('Ticket Details'))
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Box containing ticket details -->
            <div class="box">
                <div class="box-header">
                    <h5 class="box-title">{{ __('Ticket Details') }}</h5>
                </div>
                <div class="box-body">
                    <div class="grid grid-cols-12 gap-4">
                        <!-- ticket Code -->
                        <div class="col-span-12 md:col-span-4">
                            <label class="block text-sm font-bold text-gray-700">{{ __('Ticket Code') }}</label>
                            <p>{{ $ticket->code }}</p>
                        </div>
                        <!-- User -->
                        {{-- <div class="col-span-12 md:col-span-3">
                            <label class="block text-sm font-bold text-gray-700">{{ __('User') }}</label>
                            <p>{{ $ticket->user->username }}</p>
                        </div> --}}
                        <!-- Subject -->
                        <div class="col-span-12 md:col-span-4">
                            <label class="block text-sm font-bold text-gray-700">{{ __('Subject') }}</label>
                            <p>{{ $ticket->subject }}</p>
                        </div>
                        <!-- Status -->
                        <div class="col-span-12 md:col-span-4">
                            <label class="block text-sm font-bold text-gray-700">{{ __('Status') }}</label>
                            <p>
                                {!! \App\Enums\TicketStatusEnum::tryFrom($ticket->status)?->badge() !!}
                            </p>

                            <form action="{{ route('freelancer.tickets.changeStatus', $ticket->id) }}" method="POST" class="mt-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status"
                                    value="{{ $ticket->status === 'open' ? 'closed' : 'open' }}">
                                <button type="submit"
                                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                    {{ $ticket->status === 'open' ? __('Close Ticket') : __('Open Ticket') }}
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Ticket Messages -->
            <div class="box">
                <div class="box-header">
                    <h5 class="box-title">{{ __('Ticket Messages') }}</h5>
                </div>
                <div class="box-body">
                    <div class="space-y-6 mt-6">
                        @foreach ($ticket->messages as $message)
                            <div class="flex justify-center">
                                <div
                                    class="w-[75%] p-4 rounded-lg shadow {{ $message->is_admin ? 'bg-primary' : 'bg-gray-100' }}">
                                    <div class="flex items-center mb-2">
                                        <!-- Avatar -->
                                        <div class="px-3">
                                            @if ($message->messageable->avatar)
                                                <img src="{{ asset($message->messageable->avatar) }}"
                                                    alt="{{ $message->messageable->username }}"
                                                    class="w-10 h-10 rounded-full">
                                            @endif
                                        </div>
                                        <!-- Username -->
                                        <div>
                                            <span
                                                class="font-bold {{ $message->is_admin ? 'text-white' : 'text-gray-700' }}">{{ $message->messageable->username }}</span>
                                        </div>
                                    </div>
                                    <!-- Message Timestamp -->
                                    <div class="font-bold {{ $message->is_admin ? 'text-white' : 'text-gray-700' }} mb-1">
                                        {{ $message->created_at->format('d M Y h:i A') }}
                                    </div>
                                    <!-- Message Content -->
                                    <p class="text-xl {{ $message->is_admin ? 'text-white' : 'text-gray-700' }} mb-2">
                                        {{ $message->message }}</p>
                                    @if ($message->attachments->count())
                                        <div class="flex justify-start gap-1 text-xs">
                                            @foreach ($message->attachments as $attachment)
                                                <div class="p-4 rounded-lg flex items-center gap-2 shadow-sm">
                                                    <a href="{{ asset($attachment->file_path) }}" target="_blank"
                                                        class="w-16 h-16 {{ $message->is_admin ? 'bg-gray-100' : 'bg-primary text-white' }} rounded-lg flex justify-center items-center">
                                                        <i class="las la-image text-3xl"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Reply Form -->
            <div class="box">
                <div class="box-header">
                    <h5 class="box-title">{{ __('Reply to Ticket') }}</h5>
                </div>
                <div class="box-body">
                    <form action="{{ route('freelancer.tickets.reply', $ticket->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <!-- Textarea for Message -->
                            <div>
                                <label for="message"
                                    class="block text-sm font-medium text-gray-700">{{ __('Your Reply') }}</label>
                                <textarea name="message" id="message" rows="4" class="w-full p-2 border border-gray-300 rounded-lg"></textarea>
                            </div>

                            <!-- File Attachment -->
                            <div>
                                <label for="attachment"
                                    class="block text-sm font-medium text-gray-700">{{ __('Attach Files') }}</label>
                                <input type="file" name="attachment[]" id="attachment" multiple
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="flex items-center gap-2 px-4 py-3 text-white bg-primary hover:bg-blue-600 rounded-lg shadow">
                                    {{ __('send reply') }} <i class="las la-paper-plane text-lg"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
