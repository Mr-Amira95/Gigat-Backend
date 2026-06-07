@extends('layouts.master')

@section('title', 'Statistics')

@push('styles')
<style>
    .stat-card { transition: transform .15s ease, box-shadow .15s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,.08); }
    .stat-number { font-size: 2rem; font-weight: 700; line-height: 1; }
    .stat-label { font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
    .section-title {
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7280;
        margin-bottom: .75rem;
        padding-bottom: .4rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .dark .section-title { color: #9ca3af; border-color: #374151; }
    .mini-row { display: flex; justify-content: space-between; align-items: center; padding: .35rem 0; border-bottom: 1px solid #f3f4f6; }
    .dark .mini-row { border-color: #1f2937; }
    .mini-row:last-child { border-bottom: none; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    .progress-bar { height: 6px; border-radius: 3px; background: #e5e7eb; overflow: hidden; margin-top: 4px; }
    .progress-fill { height: 100%; border-radius: 3px; }
</style>
@endpush

@section('content')
<div class="content">
    <div class="main-content">

        {{-- Page Header --}}
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="!text-defaulttextcolor dark:!text-defaulttextcolor/70 dark:text-white text-[1.125rem] font-semibold">
                    Statistics
                </h3>
            </div>
            <ol class="flex items-center whitespace-nowrap">
                <li class="text-[0.813rem] ps-[0.5rem]">
                    <a class="flex items-center text-primary" href="{{ route('home.index') }}">
                        <i class="ti ti-home me-1"></i> {{ __('home') }}
                        <i class="ti ti-chevrons-right px-[0.5rem] rtl:rotate-180"></i>
                    </a>
                </li>
                <li class="text-[0.813rem] font-semibold">Statistics</li>
            </ol>
        </div>

        {{-- Filters --}}
        <div class="box mb-6">
            <div class="box-header">
                <h5 class="box-title"><i class="bx bx-filter-alt me-2"></i>Filters</h5>
            </div>
            <div class="box-body p-4">
                <form method="GET" action="{{ route('statistics.index') }}">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-3">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">From</label>
                            <input type="date" name="from" value="{{ $from }}"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-[#1f2937] dark:text-white text-sm">
                        </div>
                        <div class="col-span-12 md:col-span-3">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">To</label>
                            <input type="date" name="to" value="{{ $to }}"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-[#1f2937] dark:text-white text-sm">
                        </div>
                        <div class="col-span-12 md:col-span-3">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Platform (Visitors)</label>
                            <select name="platform"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-[#1f2937] dark:text-white text-sm">
                                <option value="">All Platforms</option>
                                @foreach (['web', 'android', 'ios'] as $p)
                                    <option value="{{ $p }}" @selected($platform === $p)>{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-12 md:col-span-3 flex items-end gap-2">
                            <button type="submit"
                                class="flex-1 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90">
                                <i class="bx bx-search me-1"></i>Apply
                            </button>
                            <a href="{{ route('statistics.index') }}"
                                class="flex-1 text-center bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Top KPI Cards ──────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

            @php
                $kpis = [
                    ['label' => 'Registered Users',  'value' => $totalUsers,       'icon' => 'bx-group',        'color' => 'text-primary'],
                    ['label' => 'Total Services',    'value' => $totalServices,    'icon' => 'bx-cube',         'color' => 'text-success'],
                    ['label' => 'Total Requests',    'value' => $totalRequests,    'icon' => 'bx-clipboard',    'color' => 'text-warning'],
                    ['label' => 'Paid Revenue',      'value' => number_format($totalRevenue, 2), 'icon' => 'bx-dollar-circle', 'color' => 'text-danger'],
                    ['label' => 'Freelancers',       'value' => $totalFreelancers, 'icon' => 'bx-user-check',   'color' => 'text-info'],
                    ['label' => 'Portfolios',        'value' => $totalPortfolios,  'icon' => 'bx-image',        'color' => 'text-purple-500'],
                    ['label' => 'Open Tickets',      'value' => $ticketsByStatus->get('open', 0), 'icon' => 'bx-support', 'color' => 'text-orange-500'],
                    ['label' => 'App Visitors',      'value' => $totalVisitors,    'icon' => 'bx-bar-chart-alt-2', 'color' => 'text-teal-500'],
                ];
            @endphp

            @foreach ($kpis as $kpi)
            <div class="stat-card border rounded-[10px] border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1f2937] p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="stat-label text-gray-400">{{ $kpi['label'] }}</span>
                    <i class="bx {{ $kpi['icon'] }} text-2xl {{ $kpi['color'] }}"></i>
                </div>
                <div class="stat-number text-defaulttextcolor dark:text-white">{{ $kpi['value'] }}</div>
            </div>
            @endforeach

        </div>

        {{-- ── Row 1: Users + Freelancers ────────────────────────────────────── --}}
        <div class="grid grid-cols-12 gap-6 mb-6">

            {{-- Users --}}
            <div class="col-span-12 md:col-span-4">
                <div class="box h-full">
                    <div class="box-header">
                        <h5 class="box-title"><i class="bx bx-group me-2 text-primary"></i>Users</h5>
                    </div>
                    <div class="box-body p-4">
                        <div class="section-title">Counts</div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Registered</span>
                            <span class="font-bold text-defaulttextcolor dark:text-white">{{ number_format($totalUsers) }}</span>
                        </div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Verified</span>
                            <span class="font-bold text-success">{{ number_format($verifiedUsers) }}</span>
                        </div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Clients (no freelancer)</span>
                            <span class="font-bold text-defaulttextcolor dark:text-white">{{ number_format($totalClients) }}</span>
                        </div>

                        @if ($genderBreakdown->isNotEmpty())
                        <div class="section-title mt-4">By Gender</div>
                        @foreach ($genderBreakdown as $row)
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $row->gender ?? 'Unknown' }}</span>
                            <span class="font-semibold text-defaulttextcolor dark:text-white">{{ number_format($row->total) }}</span>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>

            {{-- Freelancers --}}
            <div class="col-span-12 md:col-span-4">
                <div class="box h-full">
                    <div class="box-header">
                        <h5 class="box-title"><i class="bx bx-user-check me-2 text-info"></i>Freelancers</h5>
                    </div>
                    <div class="box-body p-4">
                        <div class="section-title">By Status</div>
                        @php
                            $freelancerColors = ['approved' => '#22c55e', 'pending' => '#f59e0b', 'rejected' => '#ef4444'];
                        @endphp
                        @foreach (['approved', 'pending', 'rejected'] as $st)
                        @php $cnt = $freelancersByStatus->get($st, 0); $pct = $totalFreelancers > 0 ? round($cnt / $totalFreelancers * 100) : 0; @endphp
                        <div class="mb-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="capitalize text-gray-600 dark:text-gray-400">
                                    <span class="status-dot" style="background:{{ $freelancerColors[$st] }}"></span>{{ $st }}
                                </span>
                                <span class="font-semibold text-defaulttextcolor dark:text-white">{{ number_format($cnt) }} <span class="text-xs text-gray-400">({{ $pct }}%)</span></span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:{{ $pct }}%; background:{{ $freelancerColors[$st] }}"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Visitors --}}
            <div class="col-span-12 md:col-span-4">
                <div class="box h-full">
                    <div class="box-header">
                        <h5 class="box-title"><i class="bx bx-devices me-2 text-warning"></i>App Visitors</h5>
                    </div>
                    <div class="box-body p-4">
                        <div class="section-title">Overview</div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Visitors</span>
                            <span class="font-bold text-defaulttextcolor dark:text-white">{{ number_format($totalVisitors) }}</span>
                        </div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Logged-in Users</span>
                            <span class="font-bold text-success">{{ number_format($linkedVisitors) }}</span>
                        </div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Anonymous</span>
                            <span class="font-bold text-gray-500">{{ number_format($totalVisitors - $linkedVisitors) }}</span>
                        </div>

                        <div class="section-title mt-4">By Platform</div>
                        @php $platformColors = ['web' => '#6366f1', 'android' => '#22c55e', 'ios' => '#a855f7']; @endphp
                        @foreach (['web', 'android', 'ios'] as $pl)
                        @php $cnt = $visitorsByPlatform->get($pl, 0); $tot = $visitorsByPlatform->sum(); $pct = $tot > 0 ? round($cnt / $tot * 100) : 0; @endphp
                        <div class="mini-row">
                            <span class="text-sm capitalize text-gray-600 dark:text-gray-400">
                                <span class="status-dot" style="background:{{ $platformColors[$pl] }}"></span>{{ $pl }}
                            </span>
                            <span class="font-semibold text-defaulttextcolor dark:text-white">{{ number_format($cnt) }} <span class="text-xs text-gray-400">({{ $pct }}%)</span></span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Row 2: Services + Portfolios + Quotations ─────────────────────── --}}
        <div class="grid grid-cols-12 gap-6 mb-6">

            {{-- Services --}}
            <div class="col-span-12 md:col-span-5">
                <div class="box h-full">
                    <div class="box-header">
                        <h5 class="box-title"><i class="bx bx-cube me-2 text-success"></i>Services</h5>
                    </div>
                    <div class="box-body p-4">
                        <div class="section-title">By Status</div>
                        @php $serviceColors = ['approved' => '#22c55e', 'pending' => '#f59e0b', 'rejected' => '#ef4444']; @endphp
                        @foreach (['approved', 'pending', 'rejected'] as $st)
                        @php $cnt = $servicesByStatus->get($st, 0); $pct = $totalServices > 0 ? round($cnt / $totalServices * 100) : 0; @endphp
                        <div class="mb-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="capitalize text-gray-600 dark:text-gray-400">
                                    <span class="status-dot" style="background:{{ $serviceColors[$st] }}"></span>{{ $st }}
                                </span>
                                <span class="font-semibold text-defaulttextcolor dark:text-white">{{ number_format($cnt) }} <span class="text-xs text-gray-400">({{ $pct }}%)</span></span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:{{ $pct }}%; background:{{ $serviceColors[$st] }}"></div>
                            </div>
                        </div>
                        @endforeach

                        <div class="section-title mt-4">Flags</div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Featured</span>
                            <span class="font-bold text-warning">{{ number_format($featuredServices) }}</span>
                        </div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Recommended</span>
                            <span class="font-bold text-info">{{ number_format($recommendedServices) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Portfolios --}}
            <div class="col-span-12 md:col-span-3">
                <div class="box h-full">
                    <div class="box-header">
                        <h5 class="box-title"><i class="bx bx-image me-2 text-purple-500"></i>Portfolios</h5>
                    </div>
                    <div class="box-body p-4">
                        <div class="section-title">Counts</div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total</span>
                            <span class="font-bold text-defaulttextcolor dark:text-white">{{ number_format($totalPortfolios) }}</span>
                        </div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Featured</span>
                            <span class="font-bold text-warning">{{ number_format($featuredPortfolios) }}</span>
                        </div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Regular</span>
                            <span class="font-bold text-gray-500">{{ number_format($totalPortfolios - $featuredPortfolios) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quotations --}}
            <div class="col-span-12 md:col-span-4">
                <div class="box h-full">
                    <div class="box-header">
                        <h5 class="box-title"><i class="bx bx-spreadsheet me-2 text-teal-500"></i>Quotations</h5>
                    </div>
                    <div class="box-body p-4">
                        <div class="section-title">By Status</div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total</span>
                            <span class="font-bold text-defaulttextcolor dark:text-white">{{ number_format($totalQuotations) }}</span>
                        </div>
                        @foreach (['open' => '#22c55e', 'closed' => '#6b7280'] as $st => $color)
                        <div class="mini-row">
                            <span class="text-sm capitalize text-gray-600 dark:text-gray-400">
                                <span class="status-dot" style="background:{{ $color }}"></span>{{ $st }}
                            </span>
                            <span class="font-semibold text-defaulttextcolor dark:text-white">{{ number_format($quotationsByStatus->get($st, 0)) }}</span>
                        </div>
                        @endforeach

                        {{-- Reviews --}}
                        <div class="section-title mt-4">Reviews</div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Reviews</span>
                            <span class="font-bold text-defaulttextcolor dark:text-white">{{ number_format($totalReviews) }}</span>
                        </div>
                        <div class="mini-row">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Avg Rating</span>
                            <span class="font-bold text-warning">
                                ★ {{ number_format($averageRating, 1) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Row 3: Requests + Revenue ─────────────────────────────────────── --}}
        <div class="grid grid-cols-12 gap-6 mb-6">

            {{-- Requests --}}
            <div class="col-span-12 md:col-span-6">
                <div class="box h-full">
                    <div class="box-header flex justify-between items-center">
                        <h5 class="box-title"><i class="bx bx-clipboard me-2 text-warning"></i>Requests</h5>
                        <span class="text-xs font-semibold text-success">{{ $completionRate }}% completion rate</span>
                    </div>
                    <div class="box-body p-4">
                        <div class="section-title">By Status</div>
                        @php
                            $requestColors = [
                                'pending'     => '#f59e0b',
                                'confirmed'   => '#6366f1',
                                'in_progress' => '#3b82f6',
                                'completed'   => '#22c55e',
                                'cancelled'   => '#ef4444',
                            ];
                        @endphp
                        @foreach ($requestColors as $st => $color)
                        @php $cnt = $requestsByStatus->get($st, 0); $pct = $totalRequests > 0 ? round($cnt / $totalRequests * 100) : 0; @endphp
                        <div class="mb-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="capitalize text-gray-600 dark:text-gray-400">
                                    <span class="status-dot" style="background:{{ $color }}"></span>{{ str_replace('_', ' ', $st) }}
                                </span>
                                <span class="font-semibold text-defaulttextcolor dark:text-white">{{ number_format($cnt) }} <span class="text-xs text-gray-400">({{ $pct }}%)</span></span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:{{ $pct }}%; background:{{ $color }}"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Revenue --}}
            <div class="col-span-12 md:col-span-6">
                <div class="box h-full">
                    <div class="box-header">
                        <h5 class="box-title"><i class="bx bx-dollar-circle me-2 text-danger"></i>Revenue</h5>
                    </div>
                    <div class="box-body p-4">
                        <div class="section-title">By Payment Status</div>
                        @php
                            $paymentColors = ['paid' => '#22c55e', 'unpaid' => '#f59e0b', 'failed' => '#ef4444'];
                        @endphp
                        @foreach (['paid', 'unpaid', 'failed'] as $ps)
                        @php $row = $financeByPaymentStatus->get($ps); @endphp
                        <div class="mini-row">
                            <span class="text-sm capitalize text-gray-600 dark:text-gray-400">
                                <span class="status-dot" style="background:{{ $paymentColors[$ps] }}"></span>{{ $ps }}
                                <span class="text-xs text-gray-400">({{ $row?->count ?? 0 }} orders)</span>
                            </span>
                            <span class="font-semibold text-defaulttextcolor dark:text-white">
                                {{ number_format($row?->amount ?? 0, 2) }}
                            </span>
                        </div>
                        @endforeach

                        @if ($financeByMethod->isNotEmpty())
                        <div class="section-title mt-4">By Payment Method</div>
                        @foreach ($financeByMethod as $row)
                        <div class="mini-row">
                            <span class="text-sm capitalize text-gray-600 dark:text-gray-400">{{ str_replace('_', ' ', $row->payment_method) }}</span>
                            <span class="font-semibold text-defaulttextcolor dark:text-white">
                                {{ number_format($row->amount, 2) }}
                                <span class="text-xs text-gray-400">({{ $row->count }})</span>
                            </span>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Row 4: Tickets + Reported Issues ──────────────────────────────── --}}
        <div class="grid grid-cols-12 gap-6 mb-6">

            {{-- Tickets --}}
            <div class="col-span-12 md:col-span-6">
                <div class="box h-full">
                    <div class="box-header">
                        <h5 class="box-title"><i class="bx bx-support me-2 text-orange-500"></i>Support Tickets <span class="text-xs text-gray-400 font-normal ml-1">{{ number_format($totalTickets) }} total</span></h5>
                    </div>
                    <div class="box-body p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="section-title">By Status</div>
                                @php $statusColors = ['open' => '#ef4444', 'pending' => '#f59e0b', 'resolved' => '#22c55e', 'closed' => '#6b7280']; @endphp
                                @foreach ($statusColors as $st => $color)
                                <div class="mini-row">
                                    <span class="text-sm capitalize text-gray-600 dark:text-gray-400">
                                        <span class="status-dot" style="background:{{ $color }}"></span>{{ $st }}
                                    </span>
                                    <span class="font-semibold text-defaulttextcolor dark:text-white">{{ number_format($ticketsByStatus->get($st, 0)) }}</span>
                                </div>
                                @endforeach
                            </div>
                            <div>
                                <div class="section-title">By Priority</div>
                                @php $priorityColors = ['urgent' => '#ef4444', 'high' => '#f97316', 'medium' => '#f59e0b', 'low' => '#22c55e']; @endphp
                                @foreach ($priorityColors as $pr => $color)
                                <div class="mini-row">
                                    <span class="text-sm capitalize text-gray-600 dark:text-gray-400">
                                        <span class="status-dot" style="background:{{ $color }}"></span>{{ $pr }}
                                    </span>
                                    <span class="font-semibold text-defaulttextcolor dark:text-white">{{ number_format($ticketsByPriority->get($pr, 0)) }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reported Issues --}}
            <div class="col-span-12 md:col-span-6">
                <div class="box h-full">
                    <div class="box-header">
                        <h5 class="box-title"><i class="bx bx-error-circle me-2 text-danger"></i>Reported Issues <span class="text-xs text-gray-400 font-normal ml-1">{{ number_format($totalIssues) }} total</span></h5>
                    </div>
                    <div class="box-body p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="section-title">By Status</div>
                                @php $issueStatusColors = ['pending' => '#f59e0b', 'resolved' => '#22c55e', 'cancelled' => '#6b7280']; @endphp
                                @foreach ($issueStatusColors as $st => $color)
                                <div class="mini-row">
                                    <span class="text-sm capitalize text-gray-600 dark:text-gray-400">
                                        <span class="status-dot" style="background:{{ $color }}"></span>{{ $st }}
                                    </span>
                                    <span class="font-semibold text-defaulttextcolor dark:text-white">{{ number_format($issuesByStatus->get($st, 0)) }}</span>
                                </div>
                                @endforeach
                            </div>
                            <div>
                                <div class="section-title">By Type</div>
                                @foreach ($issuesByType as $type => $count)
                                <div class="mini-row">
                                    <span class="text-sm capitalize text-gray-600 dark:text-gray-400">{{ $type }}</span>
                                    <span class="font-semibold text-defaulttextcolor dark:text-white">{{ number_format($count) }}</span>
                                </div>
                                @endforeach
                                @if ($issuesByType->isEmpty())
                                    <p class="text-sm text-gray-400 mt-2">No data</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
