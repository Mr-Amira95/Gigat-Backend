@extends('layouts.master')
@push('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <div class="content">
        <div class="main-content">
            <!-- Page Header -->
            <div class="block justify-between page-header md:flex">
                <div>
                    <h3
                        class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 dark:text-white dark:hover:text-white text-[1.125rem] font-semibold">
                        {{ __('dashboard') }}
                    </h3>
                </div>
            </div>
            <!-- Page Header Close -->

            <!-- Cards Grid -->
            <div class="w-full">
                <div class="flex items-center justify-between mt-8">
                    <h3 class="text-2xl font-semibold text-defaulttextcolor dark:text-white">{{ __('overview') }}</h3>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <form method="GET" action="{{ route('freelancer.home.index') }}" class="flex items-center gap-3">
                        <select name="filter" onchange="this.form.submit()"
                            class="border border-gray-300 rounded p-2 dark:bg-[#1f2937] dark:text-white">
                            <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}> {{ __('all') }}</option>
                            <option value="week" {{ request('filter') == 'week' ? 'selected' : '' }}>{{ __('this_week') }}</option>
                            <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>{{ __('this_month') }}</option>
                            <option value="custom" {{ request('filter') == 'custom' ? 'selected' : '' }}> {{ __('custom') }}</option>
                        </select>

                        <!-- في حال custom نعرض تاريخين -->
                        @if (request('filter') == 'custom')
                            <input type="date" name="from" value="{{ request('from') }}"
                                class="border border-gray-300 rounded p-2 dark:bg-[#1f2937] dark:text-white">
                            <input type="date" name="to" value="{{ request('to') }}"
                                class="border border-gray-300 rounded p-2 dark:bg-[#1f2937] dark:text-white">
                            <button type="submit" class="bg-primary text-white rounded px-4 py-2">Apply</button>
                        @endif
                    </form>
                </div>

                <div class="grid grid-cols-5 gap-6 mt-6">
                    <!-- Admins Card -->
                    <div
                        class="border rounded-[10px] border-gray-300 dark:border-gray-700 overflow-hidden bg-white dark:bg-[#1f2937] flex flex-col justify-between">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                            <h4 class="text-lg font-semibold text-defaulttextcolor dark:text-white">{{ __('portfolios') }}
                            </h4>
                        </div>
                        <div class="p-6 flex items-center justify-between">
                            <span
                                class="text-5xl font-bold text-defaulttextcolor dark:text-white">{{ $portfolioCount }}</span>
                            <a href="{{ route('freelancer.portfolios.create') }}"
                                class="inline-flex items-center justify-center w-10 h-10 bg-primary text-white rounded-full text-2xl">+</a>
                        </div>
                    </div>

                    <!-- Clients Card -->
                    <div
                        class="border rounded-[10px] border-gray-300 dark:border-gray-700 overflow-hidden bg-white dark:bg-[#1f2937] flex flex-col justify-between">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                            <h4 class="text-lg font-semibold text-defaulttextcolor dark:text-white">{{ __('services') }}
                            </h4>
                        </div>
                        <div class="p-6 flex items-center justify-between">
                            <span
                                class="text-5xl font-bold text-defaulttextcolor dark:text-white">{{ $servicesCount }}</span>
                            <a href="{{ route('freelancer.services.create') }}"
                                class="inline-flex items-center justify-center w-10 h-10 bg-primary text-white rounded-full text-2xl">+</a>
                        </div>
                    </div>

                    <!-- Freelancers Card -->
                    <div
                        class="border rounded-[10px] border-gray-300 dark:border-gray-700 overflow-hidden bg-white dark:bg-[#1f2937] flex flex-col justify-between">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                            <h4 class="text-lg font-semibold text-defaulttextcolor dark:text-white">
                                {{ __('support_tickets') }}</h4>
                        </div>
                        <div class="p-6 flex items-center justify-between">
                            <span
                                class="text-5xl font-bold text-defaulttextcolor dark:text-white">{{ $ticketsCount }}</span>
                            <a href="{{ route('freelancer.tickets.create') }}"
                                class="inline-flex items-center justify-center w-10 h-10 bg-primary text-white rounded-full text-2xl">+</a>
                        </div>
                    </div>

                    <!-- Roles Card -->
                    <div
                        class="border rounded-[10px] border-gray-300 dark:border-gray-700 overflow-hidden bg-white dark:bg-[#1f2937] flex flex-col justify-between">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                            <h4 class="text-lg font-semibold text-defaulttextcolor dark:text-white">{{ __('finances') }}
                            </h4>
                        </div>
                        <div class="p-6 flex items-center justify-between">
                            <span
                                class="text-5xl font-bold text-defaulttextcolor dark:text-white">{{ $financesCount }}</span>
                            {{-- <a href="{{ route('roles.create') }}"
                                class="inline-flex items-center justify-center w-10 h-10 bg-primary text-white rounded-full text-2xl">+</a> --}}
                        </div>
                    </div>


                    <!-- Service Card -->
                    <div
                        class="border rounded-[10px] border-gray-300 dark:border-gray-700 overflow-hidden bg-white dark:bg-[#1f2937] flex flex-col justify-between">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                            <h4 class="text-lg font-semibold text-defaulttextcolor dark:text-white">{{ __('requests') }}
                            </h4>
                        </div>
                        <div class="p-6 flex items-center justify-between">
                            <span
                                class="text-5xl font-bold text-defaulttextcolor dark:text-white">{{ $requestsCount }}</span>
                            {{-- <a href="{{ route('services.create') }}"
                                class="inline-flex items-center justify-center w-10 h-10 bg-primary text-white rounded-full text-2xl">+</a> --}}
                        </div>
                    </div>
                </div>



                <!-- Requests Table -->
                <div class="mt-10">
                    <h3 class="text-2xl font-semibold text-defaulttextcolor dark:text-white mb-4">
                        {{ __('latest_requests') }}</h3>
                    <div class="flex gap-2 mb-4">
                        @php
                            $statuses = ['all', 'pending', 'in_progress'];
                        @endphp

                        {{-- @foreach ($statuses as $item)
                            <a href="{{ route('freelancer.home.index', ['status' => $item]) }}"
                                class="px-4 py-2 rounded
                {{ $status == $item ? 'bg-primary text-white' : 'bg-gray-200' }}">
                                {{ ucfirst(str_replace('_', ' ', $item)) }}
                            </a>
                        @endforeach --}}
                        @foreach ($statuses as $item)
                            <a href="{{ route('freelancer.home.index', ['status' => $item]) }}"
                                class="px-4 py-2 rounded {{ $status == $item ? 'bg-primary text-white' : 'bg-gray-200' }}">
                                {{ __($item) }}
                            </a>
                        @endforeach
                    </div>


                    <div class="overflow-x-auto rounded-lg border border-gray-300 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-[#374151]">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        {{ __('request_number') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        {{ __('client') }}</th>
                                    {{-- <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Freelancer</th> --}}
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        {{ __('start_date') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        {{ __('end_date') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        {{ __('status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-[#1f2937] divide-y divide-gray-200 dark:divide-gray-600">

                                @foreach ($requests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                            <a href="{{ route('freelancer.requests.show', $request->id) }}"
                                                class="text-primary font-semibold hover:underline">
                                                #{{ $request->id }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                            {{ $request->user->username ?? '-' }}</td>
                                        {{-- <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                            {{ $request->service->user->username ?? '-' }}</td> --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                            {{ $request->start_date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                            {{ $request->end_date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if ($request->status == 'completed') bg-green-100 text-green-800
                                @elseif($request->status == 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                                {{  __($request->status) }}
                                            </span>
                                            {{-- {!! \App\Enums\RequestStatusEnum::tryFrom($request->status)?->badge() !!} --}}

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>



                <div class="mt-10">
                    <h3 class="text-2xl font-semibold text-defaulttextcolor dark:text-white mb-4">
                        {{ __('support_tickets') }}</h3>
                    {{-- <div class="flex gap-2 mb-4">
                        <a href="{{ route('home.index', ['ticket_status' => 'all']) }}"
                            class="px-4 py-2 rounded {{ request('ticket_status') == 'all' || !request('ticket_status') ? 'bg-primary text-white' : 'bg-gray-200' }}">
                            All
                        </a>
                        <a href="{{ route('home.index', ['ticket_status' => 'open']) }}"
                            class="px-4 py-2 rounded {{ request('ticket_status') == 'open' ? 'bg-primary text-white' : 'bg-gray-200' }}">
                            Open
                        </a>
                        <a href="{{ route('home.index', ['ticket_status' => 'closed']) }}"
                            class="px-4 py-2 rounded {{ request('ticket_status') == 'closed' ? 'bg-primary text-white' : 'bg-gray-200' }}">
                            Closed
                        </a>
                    </div> --}}

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-[#374151]">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    {{ __('code') }}</th>
                                {{-- <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    Client</th> --}}
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    {{ __('subject') }}</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    {{ __('opened_date') }}</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                    {{ __('status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-[#1f2937] divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($tickets as $ticket)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                        <a href="{{ route('freelancer.tickets.show', $ticket->id) }}"
                                            class="text-primary font-semibold hover:underline">
                                            #{{ $ticket->code }}
                                        </a>
                                    </td>
                                    {{-- <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                        {{ $ticket->user->username ?? '-' }}
                                    </td> --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                        {{ $ticket->subject }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                        {{ \Carbon\Carbon::parse($ticket->created_at)->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                    @if ($ticket->status == 'open') bg-yellow-100 text-yellow-800
                    @elseif($ticket->status == 'closed') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>


                <div class="mt-10">
                    <h3 class="text-2xl font-semibold text-defaulttextcolor dark:text-white mb-4">{{ __('quotations') }}
                    </h3>
                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 mt-8">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-[#374151]">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        {{ __('title') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        {{ __('description') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                         {{ __('price') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        {{ __('client') }}</th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        {{ __('request_date') }}</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        {{ __('comments') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-[#1f2937] divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach ($quotations as $quotation)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                            {{ $quotation->translation->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                            {{ Str::limit($quotation->translation->description, 50) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                            {{ $currencySymbol }}
                                            {{ \App\Utilities\CurrencyConverter::convert($quotation->price, 'USD', $currentCurrency) }}

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                            {{ $quotation->user->username ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">
                                            {{ $quotation->created_at->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-defaulttextcolor dark:text-white">

                                            <a aria-label="{{ __('add_comment') }}"
                                                href="{{ route('freelancer.quotations.comment.create', $quotation->id) }}"
                                                class="ti-btn btn-wave ti-btn-icon ti-btn-sm ti-btn-primary mx-1 rounded-pill"
                                                title="{{ __('add_comment') }}">
                                                <i class="las la-comment"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <!-- Cards Grid End -->

        </div>
    </div>
@endsection

@push('scripts')
    <!-- Load the OneSignal SDK -->
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>

    <script>
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(async function(OneSignal) {
            // Initialize OneSignal
            // await OneSignal.init({
            //     appId: "7ab59a87-79f3-46e8-af69-673331be40cc",
            //     onesignalId: "7ab59a87-79f3-46e8-af69-673331be40cc",
            //     // Optional: allow localhost for testing
            //     allowLocalhostAsSecureOrigin: true,
            // });
            // var notificationUrl = "{{ url('/freelancer/notification') }}";
            // OneSignal.Notifications.setDefaultUrl(notificationUrl);


            OneSignal.Notifications.setDefaultTitle("GIGAT");
            console.log("Notification permission status:", Notification.permission);

            // Request push permission if not already granted
            if (Notification.permission !== 'granted') {
                await OneSignal.Notifications.requestPermission();
            }


            function savePlayerIdToBackend(playerId) {
                console.log("welcome");
                fetch("/freelancer/save-player-id", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            player_id: playerId,
                            platform: "web"
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Player ID Saved:", data);
                    })
                    .catch(error => {
                        console.error("Error saving player ID:", error);
                    });
            }

            // Main logic
            let playerId = await OneSignal.User.PushSubscription.id;

            if (playerId) {
                console.log("User is subscribed, player ID:", playerId);
                document.getElementById("player_id").value = playerId;
                savePlayerIdToBackend(playerId);
            } else {
                console.log("Notifications permission not granted or no player ID yet.");

                OneSignal.User.addEventListener('subscriptionChange', async (event) => {
                    const newPlayerId = await OneSignal.User.PushSubscription.id;
                    console.log("Subscription changed — new Player ID:", newPlayerId);

                    if (newPlayerId) {
                        document.getElementById("player_id").value = newPlayerId;
                        savePlayerIdToBackend(newPlayerId);
                    }
                });
            }

            console.log("User's OneSignal Player ID:", playerId);
        });
    </script>
@endpush
