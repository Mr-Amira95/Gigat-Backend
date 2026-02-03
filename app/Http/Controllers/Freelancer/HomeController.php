<?php

namespace App\Http\Controllers\Freelancer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Finance;
use App\Models\Freelancer;
use App\Models\Portfolio;
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


        $getCount = function ($model, $from, $to, $relationPath = null) {
            $query = $model::query();

            if ($relationPath) {
                $query->whereHas($relationPath, function ($q) {
                    $q->where('user_id', auth()->id());
                });
            } else {
                $query->where('user_id', auth()->id());
            }

            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            }

            return $query->count();
        };



        $portfolioCount = $getCount(Portfolio::class, $from, $to); // has user_id
        $requestsCount  = $getCount(ModelsRequest::class, $from, $to, 'service');
        $financesCount  = $getCount(Finance::class, $from, $to, 'request.service'); // via nested relation
        $servicesCount  = $getCount(Service::class, $from, $to); // has user_id
        $ticketsCount   = $getCount(Ticket::class, $from, $to); // has user_id


        // requests table with status filter
        $requestsQuery = ModelsRequest::with(['service.user', 'user'])
            ->whereHas('service', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest();

        if ($status && $status != 'all') {
            $requestsQuery->where('status', $status);
        }

        $requests = $requestsQuery->limit(10)->get();

        // tickets table with ticket_status filter
        $ticketsQuery = Ticket::where('user_id', auth()->id())->whereIn('status', ['open'])->with(['user'])->latest();

        if ($ticketStatus && $ticketStatus != 'all') {
            $ticketsQuery->where('status', $ticketStatus);
        }

        $tickets = $ticketsQuery->limit(10)->get();

        $freelancerCategoryIds = Auth::user()->categories->pluck('id')->toArray();

        $quotations = Quotation::with(['user'])
            ->whereHas('subCategory.category', function ($q) use ($freelancerCategoryIds) {
                $q->whereIn('id', $freelancerCategoryIds);
            })
            ->whereDoesntHave('quotationComments', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest()
            ->limit(10)
            ->get();

        return view('pages-freelancer.welcome', compact(
            'filter',
            'from',
            'to',
            'requestsCount',
            'financesCount',
            'portfolioCount',
            'ticketsCount',
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


    public function changeCurrency($currency)
    {
        $availableCurrencies = Currency::pluck('code')->toArray();

        if (in_array($currency, $availableCurrencies)) {
            Session::put('currency', $currency);
        }

        return redirect()->back();
    }
}
