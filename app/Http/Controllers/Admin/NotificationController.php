<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Freelancer;
use App\Models\Notification;
use App\Models\PlayerId;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\User;
use App\Services\OneSignalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{

    public function index()
    {

        $notifications = Notification::where('sent_by_admin', true)
            ->select('title', 'body', DB::raw('count(*) as total'))
            ->groupBy('title', 'body')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.notifications.index', compact('notifications'));
    }

    public function create()
    {
        // Use your preferred scopes/filters (e.g., ->where('is_active',1))
        $categories = Category::orderBy('id', 'desc')->get();
        $services = Service::orderBy('id', 'desc')->get();
        $portfolios = Portfolio::orderBy('id', 'desc')->get();
        return view('pages.notifications.create', compact('categories', 'services', 'portfolios'));
    }

    public function searchUsers(Request $request)
    {
        $q = $request->get('q', '');
        $audience = $request->get('audience', 'all');

        $usersQuery = User::query();

        if ($audience === 'freelancer') {
            $usersQuery->whereHas('freelancer');
        } elseif ($audience === 'client') {
            $usersQuery->whereDoesntHave('freelancer');
        }

        $usersQuery->when($q !== '', function ($query) use ($q) {
            $query->where('username', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%");
        });

        $users = $usersQuery->limit(20)->get(['id', 'username', 'email']);

        return response()->json(['status' => true, 'data' => $users->map(fn ($u) => [
            'id'   => $u->id,
            'text' => "{$u->username} ({$u->email})",
        ])]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'audience'   => 'required|in:all,freelancer,client',
            'platform'   => 'required|in:all,ios,android,web',
            'title_en'   => 'required|string',
            'title_ar'   => 'required|string',
            'body_en'    => 'required|string',
            'body_ar'    => 'required|string',
            'notif_type'  => ['nullable', Rule::in(['categories', 'services', 'portfolio'])],
            'notif_id'    => ['nullable', 'string', 'max:191', Rule::requiredIf($request->filled('notif_type'))],
            'user_ids'    => ['nullable', 'array'],
            'user_ids.*'  => ['integer', 'exists:users,id'],
        ]);
        // (Optional but recommended) Ensure notif_id exists in the selected table when provided
        if ($request->filled('notif_type') && $request->filled('notif_id')) {
            $exists = match ($request->notif_type) {
                'categories' => Category::whereKey($request->notif_id)->exists(),
                'services'   => Service::whereKey($request->notif_id)->exists(),
                'portfolio'  => Portfolio::whereKey($request->notif_id)->exists(),
                default      => false,
            };
            if (! $exists) {
                return back()->withErrors(['notif_id' => __('select_item')])->withInput();
            }
        }

        // 2) Resolve audience → users
        $usersQuery = User::query();

        if ($request->audience == 'freelancer') {
            $freelancerIds = Freelancer::pluck('user_id')->toArray();
            $usersQuery->whereIn('id', $freelancerIds);
        } elseif ($request->audience == 'client') {
            $freelancerIds = Freelancer::pluck('user_id')->toArray();
            $usersQuery->whereNotIn('id', $freelancerIds);
        }

        if ($request->filled('user_ids')) {
            $usersQuery->whereIn('id', $request->user_ids);
        }

        $users = $usersQuery->pluck('id')->toArray();

        if (empty($users)) {
            return redirect()->back()->with('error', 'No users found for this audience.');
        }

        // 3) Resolve platform → player ids
        $playerIdsQuery = PlayerId::whereIn('user_id', $users)
            ->where('is_notifiable', 1);

        if ($request->platform != 'all') {
            $playerIdsQuery->where('platform', $request->platform);
        }

        $playerIds = $playerIdsQuery->pluck('player_id')->toArray();

        if (empty($playerIds)) {
            return redirect()->back()->with('error', 'No devices found for this audience and platform.');
        }

        // 4) Titles & messages
        $titles = [
            'en' => $request->title_en,
            'ar' => $request->title_ar,
        ];

        $messages = [
            'en' => $request->body_en,
            'ar' => $request->body_ar,
        ];

        // 5) Type / ID to pass & store (null if not provided)
        $type   = $request->filled('notif_type') ? $request->notif_type : null;
        $typeId = $request->filled('notif_type') ? $request->notif_id   : null;

        // 6) Send via OneSignal (include custom data)
        $response = app(OneSignalService::class)->sendNotificationToUser(
            $playerIds,
            $titles,
            $messages,
            $type,     // categories|services|portfolio|null
            $typeId    // id|null
            // 'admin',
            // null
        );

        // 7) Persist a row per user
        foreach ($users as $userId) {
            Notification::create([
                'user_id'            => $userId,
                'title'              => json_encode($titles),
                'body'               => json_encode($messages),
                'type'               => $type,      // now payload type, not "admin"
                'type_id'            => $typeId,    // related record id
                'is_read'            => false,
                'onesignal_id'       => $response['id'] ?? null,
                'response_onesignal' => json_encode($response),
                'sent_by_admin'      => true,
            ]);
        }

        return redirect()->back()->with('success',  __('notification_sent_successfully'));
    }

    //
}
