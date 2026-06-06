<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Visitor;
use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->subDays(29)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $platform  = $request->platform;
        $country   = $request->country;
        $eventName = $request->event_name;

        // ── Visitor base scope ────────────────────────────────────────────────
        $vq = Visitor::whereBetween('created_at', [$from, $to]);
        if ($platform) $vq->where('platform', $platform);
        if ($country)  $vq->where('country', $country);

        // ── Event base scope (with optional visitor filters) ──────────────────
        $eq = AnalyticsEvent::whereBetween('created_at', [$from, $to]);
        if ($platform || $country) {
            $eq->whereHas('visitor', function ($q) use ($platform, $country) {
                if ($platform) $q->where('platform', $platform);
                if ($country)  $q->where('country', $country);
            });
        }
        if ($eventName) $eq->where('event_name', $eventName);

        // ── Summary cards ─────────────────────────────────────────────────────
        $totalVisitors = (clone $vq)->count();
        $totalEvents   = (clone $eq)->count();
        $linkedUsers   = (clone $vq)->whereNotNull('user_id')->count();
        $topPlatform   = (clone $vq)
            ->select('platform', DB::raw('count(*) as cnt'))
            ->groupBy('platform')
            ->orderByDesc('cnt')
            ->value('platform') ?? '—';

        // ── Events over time (daily) ──────────────────────────────────────────
        $eventsOverTime = (clone $eq)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Platform breakdown ────────────────────────────────────────────────
        $platformBreakdown = (clone $vq)
            ->select('platform', DB::raw('count(*) as total'))
            ->groupBy('platform')
            ->get();

        // ── Top 10 events ─────────────────────────────────────────────────────
        $topEvents = (clone $eq)
            ->select('event_name', DB::raw('count(*) as total'))
            ->groupBy('event_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // ── Top 10 countries ──────────────────────────────────────────────────
        $topCountries = (clone $vq)
            ->select('country', DB::raw('count(*) as total'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // ── Recent events (paginated) ─────────────────────────────────────────
        $eventsQuery = AnalyticsEvent::with(['visitor', 'user'])
            ->whereBetween('created_at', [$from, $to]);
        if ($platform || $country) {
            $eventsQuery->whereHas('visitor', function ($q) use ($platform, $country) {
                if ($platform) $q->where('platform', $platform);
                if ($country)  $q->where('country', $country);
            });
        }
        if ($eventName) $eventsQuery->where('event_name', $eventName);
        $events = $eventsQuery->latest('created_at')->paginate(15, ['*'], 'events_page');

        // ── Recent visitors (paginated) ───────────────────────────────────────
        $visitorsQuery = Visitor::with('user')->whereBetween('created_at', [$from, $to]);
        if ($platform) $visitorsQuery->where('platform', $platform);
        if ($country)  $visitorsQuery->where('country', $country);
        $visitors = $visitorsQuery->latest()->paginate(15, ['*'], 'visitors_page');

        // ── Filter option lists ────────────────────────────────────────────────
        $countries  = Visitor::select('country')->distinct()->whereNotNull('country')->orderBy('country')->pluck('country');
        $eventNames = AnalyticsEvent::select('event_name')->distinct()->orderBy('event_name')->pluck('event_name');

        return view('pages.analytics.index', compact(
            'from', 'to', 'platform', 'country', 'eventName',
            'totalVisitors', 'totalEvents', 'linkedUsers', 'topPlatform',
            'eventsOverTime', 'platformBreakdown', 'topEvents', 'topCountries',
            'events', 'visitors',
            'countries', 'eventNames'
        ));
    }
}
