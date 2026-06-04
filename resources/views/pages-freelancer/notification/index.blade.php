@extends('layouts.master')
@section('title', __('notifications'))

@push('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Notification card */
        .notification-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
            padding: 1.25rem 1.5rem;
            transition: box-shadow 0.3s ease, background-color 0.3s ease;
            display: block;
            text-decoration: none;
            color: inherit;
            position: relative;
            border-left: 6px solid transparent;
        }

        /* Hover effect */
        .notification-card:hover {
            box-shadow: 0 4px 15px rgb(0 0 0 / 0.15);
            background-color: #f9fafb;
            border-left-color: #3b82f6;
            /* Tailwind blue-500 */
        }

        /* Unread notification */
        .notification-unread {
            background-color: #eff6ff;
            /* Tailwind blue-100 */
            font-weight: 600;
            border-left-color: #2563eb;
            /* Tailwind blue-600 */
        }

        /* Title */
        .notification-title {
            font-size: 1.125rem;
            /* 18px */
            margin-bottom: 0.25rem;
            color: #1f2937;
            /* Gray-800 */
        }

        /* Message */
        .notification-message {
            font-size: 0.95rem;
            color: #4b5563;
            /* Gray-600 */
            margin-bottom: 0.5rem;
        }

        /* Time */
        .notification-time {
            font-size: 0.85rem;
            color: #9ca3af;
            /* Gray-400 */
            position: absolute;
            top: 1.25rem;
            right: 1.5rem;
            white-space: nowrap;
        }

        [dir="rtl"] .notification-time {
            right: auto;
            left: 1.5rem;
        }

        /* New badge */
        .notification-badge {
            display: inline-block;
            background-color: #2563eb;
            /* Tailwind blue-600 */
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            position: absolute;
            bottom: 1.25rem;
            right: 1.5rem;
            user-select: none;
        }

        /* Responsive */
        @media (max-width: 640px) {

            .notification-time,
            .notification-badge {
                position: static;
                margin-top: 0.5rem;
            }

            .notification-card {
                padding-right: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content">
        <div class="main-content">
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3 class="text-[1.125rem] font-semibold">{{ __('notifications') }}</h3>
                </div>
                <ol class="flex items-center whitespace-nowrap">
                    <li>
                        <a href="{{ route('freelancer.home.index') }}"
                            class="flex items-center text-blue-600 hover:underline">
                            <i class="ti ti-home me-1"></i> {{ __('home') }}
                            <i class="ti ti-chevrons-right px-1 rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li class="text-[0.813rem] font-semibold">{{ __('notifications') }}</li>
                </ol>
            </div>
        </div>


        <div class="container">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header flex justify-center items-center">
                            <h1 class="font-bold">{{ __('notifications') }}</h1>
                        </div>
                        <div class="box-body">
                            @if ($notification->isEmpty())
                                <div class="text-center text-gray-500 py-20 text-lg italic">
                                    {{ __('No new notifications.') }}
                                </div>
                            @else
                                <div class="space-y-5">
                                    @foreach ($notification as $note)
                                        @php
                                            $titleObj = json_decode($note->title);
                                            $messageObj = json_decode($note->body);
                                            $locale = app()->getLocale();
                                            $title = $titleObj->$locale ?? __('Notification');
                                            $message = $messageObj->$locale ?? '';
                                        @endphp
                                        @php
                                            $url = '#';

                                            switch ($note->type) {
                                                case 'request':
                                                    $url = $note->type_id
                                                        ? route('freelancer.requests.show', ['id' => $note->type_id])
                                                        : '#';
                                                    break;

                                                case 'request_log':
                                                    $url = $note->type_id
                                                        ? route('freelancer.requests.logs', ['id' => $note->type_id])
                                                        : '#';
                                                    break;

                                                case 'new_freelancer':
                                                    $url = '#';
                                                    break;

                                                case 'rate':
                                                    $url = $note->type_id
                                                        ? route('freelancer.reviews.index', ['id' => $note->type_id])
                                                        : '#';
                                                    break;

                                                case 'quotation':
                                                    $url = $note->type_id
                                                        ? route('freelancer.quotations.show', ['id' => $note->type_id])
                                                        : '#';
                                                    break;

                                                case 'chat':
                                                    $url = $note->type_id
                                                        ? route('freelancer.chat.show', ['chatId' => $note->type_id])
                                                        : '#';
                                                    break;

                                                case 'call':
                                                    $url = $note->type_id
                                                        ? route('freelancer.call.answer', ['callId' => $note->type_id])
                                                        : '#';
                                                    break;

                                                case 'verified':
                                                    $url = '#';
                                                    break;

                                                case 'support':
                                                    $url = $note->type_id
                                                        ? route('freelancer.tickets.show', ['id' => $note->type_id])
                                                        : '#';
                                                    break;

                                                case 'service':
                                                    $url = $note->type_id
                                                        ? route('freelancer.services.show', ['id' => $note->type_id])
                                                        : '#';
                                                    break;

                                                case 'portfolio':
                                                    $url = $note->type_id
                                                        ? route('freelancer.portfolios.show', ['id' => $note->type_id])
                                                        : '#';
                                                    break;

                                                default:
                                                    $url = '#';
                                                    break;
                                            }
                                        @endphp
                                        <a href="javascript:void(0)"
                                            class="notification-card {{ !$note->is_read ? 'notification-unread' : '' }}"
                                            data-id="{{ $note->id }}" data-url="{{ $url }}">
                                            <h5 class="notification-title">{{ __($title) }}</h5>
                                            <p class="notification-message">{{ __($message) }}</p>
                                            <time class="notification-time" title="{{ $note->created_at }}">
                                                {{ $note->created_at->diffForHumans() }}
                                            </time>
                                      
                                            <br>
                                            @if (!$note->is_read)
                                                <span class="notification-badge">{{ __('new') }}</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification-card');

            notifications.forEach(function(card) {
                card.addEventListener('click', function(e) {
                    e.preventDefault();

                    const notificationId = this.getAttribute('data-id');
                    const redirectUrl = this.getAttribute('data-url');

                    if (!notificationId) {
                        window.location.href = redirectUrl;
                        return;
                    }

                    fetch(`/freelancer/notification/read/${notificationId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                this.classList.remove('notification-unread');
                                const badge = this.querySelector('.notification-badge');
                                if (badge) badge.remove();
                            }
                            window.location.href = redirectUrl;
                        })
                        .catch(() => {
                            window.location.href = redirectUrl;
                        });
                });
            });
        });
    </script>
@endpush
