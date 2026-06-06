@extends('layouts.master')

@section('title', 'Analytics')

@push('styles')
    <style>
        .analytics-card {
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .analytics-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,.08);
        }
        .platform-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: .75rem;
            font-weight: 600;
        }
        .badge-web     { background: #dbeafe; color: #1d4ed8; }
        .badge-android { background: #dcfce7; color: #15803d; }
        .badge-ios     { background: #f3e8ff; color: #7e22ce; }
    </style>
@endpush

@section('content')
<div class="content">
    <div class="main-content">

        {{-- ── Page Header ──────────────────────────────────────────────────── --}}
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 dark:text-white text-[1.125rem] font-semibold">
                    Analytics
                </h3>
            </div>
            <ol class="flex items-center whitespace-nowrap">
                <li class="text-[0.813rem] ps-[0.5rem]">
                    <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                        <i class="ti ti-home me-1"></i> {{ __('home') }}
                        <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                    </a>
                </li>
                <li class="text-[0.813rem] font-semibold">Analytics</li>
            </ol>
        </div>

        {{-- ── Filters ──────────────────────────────────────────────────────── --}}
        <div class="box mb-6">
            <div class="box-header">
                <h5 class="box-title"><i class="bx bx-filter-alt me-2"></i>Filters</h5>
            </div>
            <div class="box-body p-4">
                <form method="GET" action="{{ route('analytics.index') }}" id="filter-form">
                    <div class="grid grid-cols-12 gap-4">

                        {{-- Date From --}}
                        <div class="col-span-12 md:col-span-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">From</label>
                            <input type="date" name="from" value="{{ request('from', $from->toDateString()) }}"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-[#1f2937] dark:text-white text-sm">
                        </div>

                        {{-- Date To --}}
                        <div class="col-span-12 md:col-span-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">To</label>
                            <input type="date" name="to" value="{{ request('to', $to->toDateString()) }}"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-[#1f2937] dark:text-white text-sm">
                        </div>

                        {{-- Platform --}}
                        <div class="col-span-12 md:col-span-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Platform</label>
                            <select name="platform"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-[#1f2937] dark:text-white text-sm">
                                <option value="">All Platforms</option>
                                @foreach (['web', 'android', 'ios'] as $p)
                                    <option value="{{ $p }}" @selected(request('platform') === $p)>{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Country --}}
                        <div class="col-span-12 md:col-span-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Country</label>
                            <select name="country"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-[#1f2937] dark:text-white text-sm">
                                <option value="">All Countries</option>
                                @foreach ($countries as $c)
                                    <option value="{{ $c }}" @selected(request('country') === $c)>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Event Name --}}
                        <div class="col-span-12 md:col-span-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Event</label>
                            <select name="event_name"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-[#1f2937] dark:text-white text-sm">
                                <option value="">All Events</option>
                                @foreach ($eventNames as $e)
                                    <option value="{{ $e }}" @selected(request('event_name') === $e)>{{ $e }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Actions --}}
                        <div class="col-span-12 md:col-span-2 flex items-end gap-2">
                            <button type="submit"
                                class="flex-1 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90">
                                <i class="bx bx-search me-1"></i>Apply
                            </button>
                            <a href="{{ route('analytics.index') }}"
                                class="flex-1 text-center bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200">
                                Reset
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- ── Summary Cards ────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-4 gap-6 mb-6">

            <div class="analytics-card border rounded-[10px] border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1f2937] flex flex-col justify-between">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex items-center gap-2">
                    <i class="bx bx-user-circle text-primary text-xl"></i>
                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Visitors</h4>
                </div>
                <div class="p-6">
                    <span class="text-4xl font-bold text-defaulttextcolor dark:text-white">{{ number_format($totalVisitors) }}</span>
                </div>
            </div>

            <div class="analytics-card border rounded-[10px] border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1f2937] flex flex-col justify-between">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex items-center gap-2">
                    <i class="bx bx-pulse text-success text-xl"></i>
                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Events</h4>
                </div>
                <div class="p-6">
                    <span class="text-4xl font-bold text-defaulttextcolor dark:text-white">{{ number_format($totalEvents) }}</span>
                </div>
            </div>

            <div class="analytics-card border rounded-[10px] border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1f2937] flex flex-col justify-between">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex items-center gap-2">
                    <i class="bx bx-link text-warning text-xl"></i>
                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Linked Users</h4>
                </div>
                <div class="p-6">
                    <span class="text-4xl font-bold text-defaulttextcolor dark:text-white">{{ number_format($linkedUsers) }}</span>
                    @if ($totalVisitors > 0)
                        <p class="text-xs text-gray-400 mt-1">{{ round($linkedUsers / $totalVisitors * 100) }}% of visitors</p>
                    @endif
                </div>
            </div>

            <div class="analytics-card border rounded-[10px] border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1f2937] flex flex-col justify-between">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex items-center gap-2">
                    <i class="bx bx-devices text-danger text-xl"></i>
                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Top Platform</h4>
                </div>
                <div class="p-6">
                    <span class="platform-badge badge-{{ $topPlatform }}">{{ ucfirst($topPlatform) }}</span>
                </div>
            </div>

        </div>

        {{-- ── Charts Row 1: Events Over Time + Platform Breakdown ─────────── --}}
        <div class="grid grid-cols-12 gap-6 mb-6">

            <div class="col-span-12 md:col-span-8">
                <div class="box h-full">
                    <div class="box-header">
                        <h5 class="box-title">Events Over Time</h5>
                        <span class="text-xs text-gray-400">Daily count in selected range</span>
                    </div>
                    <div class="box-body p-4">
                        <canvas id="eventsOverTimeChart" height="110"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-span-12 md:col-span-4">
                <div class="box h-full">
                    <div class="box-header">
                        <h5 class="box-title">Platform Breakdown</h5>
                    </div>
                    <div class="box-body p-4 flex items-center justify-center">
                        <canvas id="platformChart" height="200"></canvas>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Charts Row 2: Top Events + Top Countries ─────────────────────── --}}
        <div class="grid grid-cols-12 gap-6 mb-6">

            <div class="col-span-12 md:col-span-7">
                <div class="box">
                    <div class="box-header">
                        <h5 class="box-title">Top 10 Events</h5>
                    </div>
                    <div class="box-body p-4">
                        <canvas id="topEventsChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-span-12 md:col-span-5">
                <div class="box">
                    <div class="box-header">
                        <h5 class="box-title">Top Countries</h5>
                    </div>
                    <div class="box-body">
                        @if ($topCountries->isEmpty())
                            <div class="p-6 text-center text-gray-400 text-sm">No country data available</div>
                        @else
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">#</th>
                                        <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Country</th>
                                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 font-medium">Visitors</th>
                                        <th class="px-4 py-3 text-right text-gray-500 dark:text-gray-400 font-medium">Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topCountries as $i => $row)
                                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/40">
                                            <td class="px-4 py-2 text-gray-400">{{ $i + 1 }}</td>
                                            <td class="px-4 py-2 font-medium text-defaulttextcolor dark:text-white">{{ $row->country }}</td>
                                            <td class="px-4 py-2 text-right">{{ number_format($row->total) }}</td>
                                            <td class="px-4 py-2 text-right text-gray-400">
                                                {{ $totalVisitors > 0 ? round($row->total / $totalVisitors * 100, 1) : 0 }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Recent Events Table ───────────────────────────────────────────── --}}
        <div class="box mb-6">
            <div class="box-header flex justify-between items-center">
                <h5 class="box-title">Recent Events</h5>
                <span class="text-xs text-gray-400">{{ $events->total() }} total</span>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Visitor UUID</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">User</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Event</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Screen</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Platform</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Country</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Metadata</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($events as $event)
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/40">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">
                                        {{ substr($event->visitor->visitor_uuid ?? '—', 0, 12) }}…
                                    </td>
                                    <td class="px-4 py-3 text-defaulttextcolor dark:text-white">
                                        {{ $event->user?->name ?? $event->user?->username ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 text-xs font-medium px-2 py-1 rounded">
                                            {{ $event->event_name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $event->screen_name ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($event->visitor?->platform)
                                            <span class="platform-badge badge-{{ $event->visitor->platform }}">
                                                {{ ucfirst($event->visitor->platform) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $event->visitor?->country ?? '—' }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-400 max-w-[160px] truncate" title="{{ json_encode($event->metadata) }}">
                                        {{ $event->metadata ? Str::limit(json_encode($event->metadata), 40) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">
                                        {{ $event->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">No events found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($events->hasPages())
                <div class="box-footer p-4">
                    {{ $events->appends(request()->except('events_page'))->links() }}
                </div>
            @endif
        </div>

        {{-- ── Recent Visitors Table ────────────────────────────────────────── --}}
        <div class="box mb-6">
            <div class="box-header flex justify-between items-center">
                <h5 class="box-title">Recent Visitors</h5>
                <span class="text-xs text-gray-400">{{ $visitors->total() }} total</span>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Visitor UUID</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">User</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Platform</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Country</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Device OS</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">Browser</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">IP Address</th>
                                <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">First Seen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($visitors as $visitor)
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/40">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">
                                        {{ substr($visitor->visitor_uuid, 0, 12) }}…
                                    </td>
                                    <td class="px-4 py-3 text-defaulttextcolor dark:text-white">
                                        {{ $visitor->user?->name ?? $visitor->user?->username ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="platform-badge badge-{{ $visitor->platform }}">
                                            {{ ucfirst($visitor->platform) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $visitor->country ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $visitor->device_os ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $visitor->device_browser ?? '—' }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $visitor->ip_address ?? '—' }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">
                                        {{ $visitor->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">No visitors found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($visitors->hasPages())
                <div class="box-footer p-4">
                    {{ $visitors->appends(request()->except('visitors_page'))->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor  = isDark ? 'rgba(255,255,255,.07)' : 'rgba(0,0,0,.06)';
    const labelColor = isDark ? '#9ca3af' : '#6b7280';
    const fontFamily = "'Inter', 'ui-sans-serif', sans-serif";

    // ── Events Over Time ────────────────────────────────────────────────────
    const eventsOverTimeData = @json($eventsOverTime);

    new Chart(document.getElementById('eventsOverTimeChart'), {
        type: 'line',
        data: {
            labels: eventsOverTimeData.map(r => r.date),
            datasets: [{
                label: 'Events',
                data: eventsOverTimeData.map(r => r.total),
                fill: true,
                backgroundColor: 'rgba(99,102,241,.12)',
                borderColor: '#6366f1',
                borderWidth: 2,
                pointBackgroundColor: '#6366f1',
                pointRadius: 3,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: {
                    grid: { color: gridColor },
                    ticks: { color: labelColor, font: { family: fontFamily, size: 11 }, maxTicksLimit: 10 }
                },
                y: {
                    grid: { color: gridColor },
                    ticks: { color: labelColor, font: { family: fontFamily, size: 11 }, precision: 0 },
                    beginAtZero: true
                }
            }
        }
    });

    // ── Platform Breakdown ──────────────────────────────────────────────────
    const platformData = @json($platformBreakdown);
    const platformColors = { web: '#6366f1', android: '#22c55e', ios: '#a855f7' };

    new Chart(document.getElementById('platformChart'), {
        type: 'doughnut',
        data: {
            labels: platformData.map(r => r.platform.charAt(0).toUpperCase() + r.platform.slice(1)),
            datasets: [{
                data: platformData.map(r => r.total),
                backgroundColor: platformData.map(r => platformColors[r.platform] ?? '#94a3b8'),
                borderWidth: 2,
                borderColor: isDark ? '#1f2937' : '#ffffff',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: labelColor, font: { family: fontFamily, size: 12 }, padding: 16 }
                }
            },
            cutout: '65%',
        }
    });

    // ── Top Events ──────────────────────────────────────────────────────────
    const topEventsData = @json($topEvents);

    new Chart(document.getElementById('topEventsChart'), {
        type: 'bar',
        data: {
            labels: topEventsData.map(r => r.event_name),
            datasets: [{
                label: 'Count',
                data: topEventsData.map(r => r.total),
                backgroundColor: 'rgba(99,102,241,.75)',
                borderColor: '#6366f1',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false },
            },
            scales: {
                x: {
                    grid: { color: gridColor },
                    ticks: { color: labelColor, font: { family: fontFamily, size: 11 }, precision: 0 },
                    beginAtZero: true
                },
                y: {
                    grid: { color: 'transparent' },
                    ticks: { color: labelColor, font: { family: fontFamily, size: 11 } }
                }
            }
        }
    });
})();
</script>
@endpush
