<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     // Load clients (users without freelancer relation)
    //     $clients = User::whereDoesntHave('freelancer')
    //         ->select('id', 'username')
    //         ->orderBy('username')
    //         ->get();

    //     $freelancers = collect();

    //     if ($request->filled('client_id')) {

    //         $clientId = $request->client_id;

    //         $chatUserIds = Chat::where('user_id_one', $clientId)
    //             ->orWhere('user_id_two', $clientId)
    //             ->get()
    //             ->map(function ($chat) use ($clientId) {
    //                 return $chat->user_id_one == $clientId
    //                     ? $chat->user_id_two
    //                     : $chat->user_id_one;
    //             })
    //             ->unique();

    //         // Load freelancers correctly
    //         $freelancers = User::whereIn('id', $chatUserIds)
    //             ->whereHas('freelancer')
    //             ->select('id', 'username')
    //             ->orderBy('username')
    //             ->get();
    //     }

    //     return view('pages.chats.index', compact('clients', 'freelancers'));
    // }

    public function index(Request $request)
    {
        // 1️⃣ Load all clients (users without freelancer relation)
        $clients = User::whereDoesntHave('freelancer')
            ->select('id', 'username')
            ->orderBy('username')
            ->get();

        $freelancers = collect();
        $messages    = collect();
        $client      = null;
        $freelancer  = null;

        /*
    |--------------------------------------------------------------------------
    | If client selected → load related freelancers
    |--------------------------------------------------------------------------
    */
        if ($request->filled('client_id')) {

            $clientId = $request->client_id;

            // Get users that chatted with this client
            $chatUserIds = Chat::where('user_id_one', $clientId)
                ->orWhere('user_id_two', $clientId)
                ->get()
                ->map(function ($chat) use ($clientId) {
                    return $chat->user_id_one == $clientId
                        ? $chat->user_id_two
                        : $chat->user_id_one;
                })
                ->unique();

            // Only freelancers
            $freelancers = User::whereIn('id', $chatUserIds)
                ->whereHas('freelancer')
                ->select('id', 'username')
                ->orderBy('username')
                ->get();
        }

        /*
    |--------------------------------------------------------------------------
    | If both selected → load chat messages
    |--------------------------------------------------------------------------
    */
        if ($request->filled('client_id') && $request->filled('freelancer_id')) {

            $clientId     = $request->client_id;
            $freelancerId = $request->freelancer_id;

            $client     = User::findOrFail($clientId);
            $freelancer = User::findOrFail($freelancerId);

            $chat = Chat::where(function ($q) use ($clientId, $freelancerId) {
                $q->where('user_id_one', $clientId)
                    ->where('user_id_two', $freelancerId);
            })
                ->orWhere(function ($q) use ($clientId, $freelancerId) {
                    $q->where('user_id_one', $freelancerId)
                        ->where('user_id_two', $clientId);
                })
                ->first();

            if ($chat) {
                $messages = ChatMessage::where('chat_id', $chat->id)
                    ->when($request->filled('created_date_from'), function ($query) use ($request) {
                        $query->whereDate('created_at', '>=', $request->created_date_from);
                    })
                    ->when($request->filled('created_date_to'), function ($query) use ($request) {
                        $query->whereDate('created_at', '<=', $request->created_date_to);
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return view('pages.chats.index', compact(
            'clients',
            'freelancers',
            'messages',
            'client',
            'freelancer'
        ));
    }

    public function show(Request $request)
    {
        $clientId = $request->client_id;
        $freelancerId = $request->freelancer_id;

        if (!$clientId || !$freelancerId) {
            return redirect()->route('chats.index')
                ->with('error', 'Client and Freelancer are required.');
        }

        // Get users
        $client = User::findOrFail($clientId);
        $freelancer = User::findOrFail($freelancerId);

        // Find chat
        $chat = Chat::where(function ($q) use ($clientId, $freelancerId) {
            $q->where('user_id_one', $clientId)
                ->where('user_id_two', $freelancerId);
        })->orWhere(function ($q) use ($clientId, $freelancerId) {
            $q->where('user_id_one', $freelancerId)
                ->where('user_id_two', $clientId);
        })->first();

        $messages = collect();

        if ($chat) {
            $messages = ChatMessage::where('chat_id', $chat->id)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('pages.chats.show', compact(
            'messages',
            'client',
            'freelancer'
        ));
    }

    public function getFreelancersByClient(Request $request)
    {
        $clientId = $request->client_id;

        if (!$clientId) {
            return response()->json([]);
        }

        // Get freelancer IDs that have chats with this client
        $freelancerIds = Chat::where('user_id_one', $clientId)
            ->pluck('user_id_two')
            ->merge(
                Chat::where('user_id_two', $clientId)->pluck('user_id_one')
            )
            ->unique()
            ->values();

        $freelancers = User::whereIn('id', $freelancerIds)->get(['id', 'username']);

        return response()->json($freelancers);
    }
}
