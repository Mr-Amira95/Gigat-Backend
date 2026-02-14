@extends('layouts.master')

@section('title', __('chat'))

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        #chatBody {
            overflow-y: auto;
            overflow-x: hidden;
            word-wrap: break-word;
        }

        /* Optional nice scrollbar */
        #chatBody::-webkit-scrollbar {
            width: 6px;
        }

        #chatBody::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="main-content">

            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('chat') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li class="text-[0.813rem] ps-[0.5rem]">
                        <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
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
                    <div class="box">

                        <div class="box-header">
                            <h5 class="box-title">{{ __('chat_history') }}</h5>
                        </div>

                        <!-- FILTER SECTION -->
                        <div class="box-footer border-t p-4">
                            <form method="GET" action="{{ route('chats.index') }}">
                                <div class="flex flex-wrap gap-4 items-end">


                                    {{-- Client --}}
                                    <div class="w-48 mx-3" style="margin-top: 7px;">
                                        <select id="clientSelect" name="client_id"
                                            class="w-48 px-2 py-2 mt-1 block rounded-lg border-gray-300 text-gray-500">
                                            <option value="">{{ __('select_client') }}</option>
                                            @foreach ($clients as $clientItem)
                                                <option value="{{ $clientItem->id }}"
                                                    {{ request('client_id') == $clientItem->id ? 'selected' : '' }}>
                                                    {{ $clientItem->username }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Freelancer --}}
                                    <div class="w-48 mx-3" style="margin-top: 7px;">
                                        <select id="freelancerSelect" name="freelancer_id"
                                            class="w-48 px-2 py-2 mt-1 block rounded-lg border-gray-300 text-gray-500">
                                            <option value="">{{ __('select_freelancer') }}</option>
                                            @foreach ($freelancers as $freelancerItem)
                                                <option value="{{ $freelancerItem->id }}"
                                                    {{ request('freelancer_id') == $freelancerItem->id ? 'selected' : '' }}>
                                                    {{ $freelancerItem->username }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- Submit --}}
                                    <button type="submit"
                                        class="flex justify-center items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/80">
                                        {{ __('show_chat') }}
                                    </button>

                                    {{-- Reset --}}
                                    <a href="{{ route('chats.index') }}"
                                        class="flex justify-center items-center px-4 py-2 bg-danger text-white rounded-lg hover:bg-red-600">
                                        {{ __('reset_filter') }}
                                    </a>


                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <!-- CHAT DISPLAY SECTION -->
                @if (isset($client) && isset($freelancer))

                    <div class="col-span-12">
                        <div class="box flex flex-col overflow-hidden" style="height: calc(100vh - 220px);">

                            {{-- Header --}}
                            <div class="box-header shrink-0 border-b">
                                <h5 class="box-title">
                                    {{ __('chat_between') }}
                                    {{-- @dd($client) --}}
                                    <span class="text-primary">{{ $client->username }}</span>
                                    &
                                    <span class="text-primary">{{ $freelancer->username }}</span>
                                </h5>
                            </div>

                            {{-- Messages --}}
                            <div id="chatBody" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">

                                @forelse ($messages as $message)
                                    <div
                                        class="flex {{ $message->sender_id == $client->id ? 'justify-start' : 'justify-end' }}">

                                        <div
                                            class="max-w-xs p-3 rounded-lg shadow
                        {{ $message->sender_id == $client->id ? 'bg-white border' : 'bg-primary text-white' }}">

                                            <div class="text-xs font-semibold mb-1 opacity-70">
                                                {{ $message->sender_id == $client->id
                                                    ? $client->username . ' (' . __('client') . ')'
                                                    : $freelancer->username . ' (' . __('freelancer') . ')' }}
                                            </div>

                                            @if ($message->message)
                                                <p class="text-sm break-words">
                                                    {{ $message->message }}
                                                </p>
                                            @endif

                                            <span class="block text-xs mt-2 opacity-60">
                                                {{ $message->created_at->locale(app()->getLocale())->translatedFormat('d F Y - h:i A') }}
                                            </span>

                                        </div>
                                    </div>

                                @empty
                                    <div class="text-center text-gray-500">
                                        {{ __('no_messages_found') }}
                                    </div>
                                @endforelse

                            </div>

                        </div>
                    </div>
                @endif





            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            // Enable search
            $('#clientSelect').select2({
                placeholder: "{{ __('select_client') }}",
                allowClear: true,
                width: '100%'
            });

            $('#freelancerSelect').select2({
                placeholder: "{{ __('select_freelancer') }}",
                allowClear: true,
                width: '100%'
            });

            // Load freelancers by client
            $('#clientSelect').on('change', function() {
                let clientId = $(this).val();
                let freelancerSelect = $('#freelancerSelect');

                freelancerSelect.empty().trigger('change');

                if (clientId) {
                    $.ajax({
                        url: "{{ route('chats.freelancersByClient') }}",
                        type: "GET",
                        data: {
                            client_id: clientId
                        },
                        success: function(data) {
                            freelancerSelect.append(
                                '<option value="">{{ __('select_freelancer') }}</option>'
                            );

                            $.each(data, function(index, freelancer) {
                                freelancerSelect.append(
                                    `<option value="${freelancer.id}">${freelancer.username}</option>`
                                );
                            });

                            freelancerSelect.trigger('change');
                        }
                    });
                }
            });

        });
        window.addEventListener('load', function() {
            const chatBody = document.getElementById('chatBody');
            if (chatBody) {
                chatBody.scrollTop = chatBody.scrollHeight;
            }
        });
    </script>
@endpush
