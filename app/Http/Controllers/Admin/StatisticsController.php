<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Models\Freelancer;
use App\Models\Portfolio;
use App\Models\Quotation;
use App\Models\ReportedIssue;
use App\Models\Request as ModelsRequest;
use App\Models\Review;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to', now()->toDateString());
        $platform = $request->input('platform');

        $cacheKey = 'statistics_' . md5($from . '|' . $to . '|' . ($platform ?? ''));

        $stats = Cache::remember($cacheKey, 120, function () use ($from, $to, $platform) {

            $dateFilter = fn ($q) => $q->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

            // ── Users ────────────────────────────────────────────────────────
            $usersQ        = $dateFilter(User::query());
            $totalUsers    = $usersQ->count();
            $verifiedUsers = $dateFilter(User::query())->whereNotNull('verified_at')->count();
            $genderBreakdown = $dateFilter(User::query())
                ->select('gender', DB::raw('count(*) as total'))
                ->groupBy('gender')
                ->get();

            // ── Freelancers ──────────────────────────────────────────────────
            $totalFreelancers    = $dateFilter(Freelancer::query())->count();
            $freelancersByStatus = $dateFilter(Freelancer::query())
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');

            // Clients = users who have NO freelancer profile
            $totalClients = $dateFilter(User::query())
                ->doesntHave('freelancer')
                ->count();

            // ── Services ────────────────────────────────────────────────────
            $totalServices    = $dateFilter(Service::query())->count();
            $servicesByStatus = $dateFilter(Service::query())
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
            $featuredServices    = $dateFilter(Service::query())->where('is_featured', true)->count();
            $recommendedServices = $dateFilter(Service::query())->where('is_recommended', true)->count();

            // ── Portfolios ───────────────────────────────────────────────────
            $totalPortfolios   = $dateFilter(Portfolio::query())->count();
            $featuredPortfolios = $dateFilter(Portfolio::query())->where('is_featured', true)->count();

            // ── Requests ─────────────────────────────────────────────────────
            $totalRequests    = $dateFilter(ModelsRequest::query())->count();
            $requestsByStatus = $dateFilter(ModelsRequest::query())
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
            $completedRequests = $requestsByStatus->get('completed', 0);
            $completionRate    = $totalRequests > 0 ? round($completedRequests / $totalRequests * 100) : 0;

            // ── Quotations ───────────────────────────────────────────────────
            $totalQuotations    = $dateFilter(Quotation::query())->count();
            $quotationsByStatus = $dateFilter(Quotation::query())
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');

            // ── Revenue ──────────────────────────────────────────────────────
            $totalRevenue = $dateFilter(Finance::query())->where('payment_status', 'paid')->sum('total');
            $financeByPaymentStatus = $dateFilter(Finance::query())
                ->select('payment_status', DB::raw('count(*) as count'), DB::raw('sum(total) as amount'))
                ->groupBy('payment_status')
                ->get()
                ->keyBy('payment_status');
            $financeByMethod = $dateFilter(Finance::query())
                ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total) as amount'))
                ->whereNotNull('payment_method')
                ->groupBy('payment_method')
                ->get();

            // ── Reviews ──────────────────────────────────────────────────────
            $totalReviews  = $dateFilter(Review::query())->count();
            $averageRating = $dateFilter(Review::query())->avg('rating') ?? 0;

            // ── Tickets ──────────────────────────────────────────────────────
            $totalTickets    = $dateFilter(Ticket::query())->count();
            $ticketsByStatus = $dateFilter(Ticket::query())
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
            $ticketsByPriority = $dateFilter(Ticket::query())
                ->select('priority', DB::raw('count(*) as total'))
                ->groupBy('priority')
                ->pluck('total', 'priority');

            // ── Reported Issues ───────────────────────────────────────────────
            $totalIssues    = $dateFilter(ReportedIssue::query())->count();
            $issuesByStatus = $dateFilter(ReportedIssue::query())
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
            $issuesByType = $dateFilter(ReportedIssue::query())
                ->select('type', DB::raw('count(*) as total'))
                ->groupBy('type')
                ->pluck('total', 'type');

            // ── Visitors (analytics) ──────────────────────────────────────────
            $visitorsQ = $dateFilter(Visitor::query());
            if ($platform) {
                $visitorsQ->where('platform', $platform);
            }
            $totalVisitors  = (clone $visitorsQ)->count();
            $linkedVisitors = (clone $visitorsQ)->whereNotNull('user_id')->count();
            $visitorsByPlatform = $dateFilter(Visitor::query())
                ->select('platform', DB::raw('count(*) as total'))
                ->groupBy('platform')
                ->pluck('total', 'platform');

            return compact(
                'totalUsers', 'verifiedUsers', 'genderBreakdown',
                'totalFreelancers', 'freelancersByStatus', 'totalClients',
                'totalServices', 'servicesByStatus', 'featuredServices', 'recommendedServices',
                'totalPortfolios', 'featuredPortfolios',
                'totalRequests', 'requestsByStatus', 'completionRate',
                'totalQuotations', 'quotationsByStatus',
                'totalRevenue', 'financeByPaymentStatus', 'financeByMethod',
                'totalReviews', 'averageRating',
                'totalTickets', 'ticketsByStatus', 'ticketsByPriority',
                'totalIssues', 'issuesByStatus', 'issuesByType',
                'totalVisitors', 'linkedVisitors', 'visitorsByPlatform'
            );
        });

        return view('pages.statistics.index', array_merge($stats, compact('from', 'to', 'platform')));
    }
}
