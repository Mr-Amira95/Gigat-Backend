<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Freelancer;
use App\Models\Quotation;
use App\Models\Request as ModelsRequest;
use App\Models\Service;
use App\Models\SubCategory;
use App\Models\Tag;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');
        $status = $request->input('status', 'all');
        $ticketStatus = $request->input('ticket_status', 'all'); // ← اضفنا ticket_status

        $from = null;
        $to = null;

        if ($filter == 'week') {
            $from = now()->startOfWeek();
            $to = now()->endOfWeek();
        } elseif ($filter == 'month') {
            $from = now()->startOfMonth();
            $to = now()->endOfMonth();
        } elseif ($filter == 'custom') {
            $from = $request->input('from');
            $to = $request->input('to');
        }

        $getCount = function ($model, $from, $to) {
            if ($from && $to) {
                return $model::whereBetween('created_at', [$from, $to])->count();
            }
            return $model::count();
        };

        $adminsCount = $getCount(Admin::class, $from, $to);
        $clientsCount = $getCount(User::class, $from, $to) - $getCount(Freelancer::class, $from, $to);
        $freelancersCount = $getCount(Freelancer::class, $from, $to);
        $rolesCount = $getCount(Role::class, $from, $to);
        $categoriesCount = $getCount(Category::class, $from, $to);
        $subCategoriesCount = $getCount(SubCategory::class, $from, $to);
        $tagsCount = $getCount(Tag::class, $from, $to);
        $servicesCount = $getCount(Service::class, $from, $to);

        // requests table with status filter
        $requestsQuery = ModelsRequest::with(['service.user', 'user'])->latest();

        if ($status && $status != 'all') {
            $requestsQuery->where('status', $status);
        }

        $requests = $requestsQuery->limit(10)->get();

        // tickets table with ticket_status filter
        $ticketsQuery = Ticket::with(['user'])->latest();

        if ($ticketStatus && $ticketStatus != 'all') {
            $ticketsQuery->where('status', $ticketStatus);
        }

        $tickets = $ticketsQuery->limit(10)->get();
        $quotations = Quotation::with(['user'])
            ->withCount('quotationComments')
            ->latest()
            ->limit(10)
            ->get();


        return view('welcome', compact(
            'filter',
            'from',
            'to',
            'adminsCount',
            'clientsCount',
            'freelancersCount',
            'rolesCount',
            'categoriesCount',
            'subCategoriesCount',
            'tagsCount',
            'servicesCount',
            'requests',
            'status',
            'ticketStatus',
            'tickets',
                'quotations'

        ));
    }


    public function changeLocale($locale)
    {
        $availableLocales = ['en', 'ar'];

        if (in_array($locale, $availableLocales)) {
            Session::put('locale', $locale);
            App::setLocale($locale);
        }
        return redirect()->back();
    }
}
