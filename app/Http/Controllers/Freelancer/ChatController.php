<?php

namespace App\Http\Controllers\Freelancer;

use App\Events\PusherNewMessage;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\PlayerId;
use App\Models\User;
use App\Services\NoticeService;
use App\Services\OneSignalService;
use App\Utilities\FileManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{

    protected $noticeService;

    public function __construct(NoticeService $noticeService)
    {
        $this->noticeService    = $noticeService;
    }

    public function index()
    {
        $userId = auth()->id();
        $chats = Chat::with(['userOne', 'lastMessage', 'userTwo'])
            ->where(function ($q) use ($userId) {
                $q->where('user_id_one', $userId)
                    ->where('user_one_flag', '!=', 'deleted')
                    ->orWhere(function ($q2) use ($userId) {
                        $q2->where('user_id_two', $userId)
                            ->where('user_two_flag', '!=', 'deleted');
                    });
            })
            ->withCount(['messages as last_message_created_at' => function ($query) {
                $query->select(DB::raw('MAX(created_at)'));
            }])
            ->orderByDesc('last_message_created_at')
            ->get();


        return view('pages-freelancer.chat.index', compact('chats'));
    }



    public function showChat($id)
    {
        $chat = Chat::with(['messages.sender'])->findOrFail($id);
        $authId = auth()->id();

        $otherUser = $chat->user_id_one === auth()->id()
            ? $chat->userTwo
            : $chat->userOne;

        // Build base query for messages
        $messagesQuery = $chat->messages()->orderBy('created_at');

        // If chat was deleted, only show messages after deleted_at
        if ($chat->user_id_one == $authId && $chat->user_one_deleted_at) {
            $messagesQuery->where('created_at', '>', $chat->user_one_deleted_at);
        }
        if ($chat->user_id_two == $authId && $chat->user_two_deleted_at) {
            $messagesQuery->where('created_at', '>', $chat->user_two_deleted_at);
        }

        $messages = $messagesQuery->get();

        return view('pages-freelancer.chat.showChat', compact('chat', 'messages', 'otherUser'));
    }

    public function sendMessage(Request $request, $chatId)
    {
        $request->validate([
            'message'     => 'nullable|string|max:1000',
            'attachment'  => 'nullable|file|max:51200',
            'audio'       => 'nullable|file|mimes:mp3,m4a,wav,ogg|max:51200',
        ]);

        $authId = auth()->id();

        $message = new ChatMessage();
        $message->chat_id   = $chatId;
        $message->sender_id = $authId;
        $message->message   = $request->message;

        // Handle attachments
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = FileManager::upload('chat_attachments', $file);

            $mimeType = $file->getMimeType();
            $type = 'file'; // default

            if (str_starts_with($mimeType, 'image/')) {
                $type = 'image';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $type = 'video';
            } elseif (str_starts_with($mimeType, 'audio/')) {
                $type = 'audio';
            }

            $message->attachment_url  = $path;
            $message->attachment_type = $type;
        }

        // Handle separate audio input
        if ($request->hasFile('audio')) {
            $file = $request->file('audio');
            $path = FileManager::upload('chat_attachments', $file);

            $message->attachment_url  = $path;
            $message->attachment_type = 'audio';
        }

        $message->save();

        // Broadcast to others
        broadcast(new PusherNewMessage($message))->toOthers();

        $chat = Chat::findOrFail($chatId);

        // Reset deleted flag if recipient deleted this chat
        if ($chat->user_id_one != $authId && $chat->user_one_flag === 'deleted') {
            $chat->user_one_flag = 'normal';
        }
        if ($chat->user_id_two != $authId && $chat->user_two_flag === 'deleted') {
            $chat->user_two_flag = 'normal';
        }
        $chat->touch();
        $chat->save();

        // Determine recipient
        $recipientId = $chat->user_id_one == $authId ? $chat->user_id_two : $chat->user_id_one;
        $recipient   = User::find($recipientId);

        // Skip notification if recipient flagged chat as spam
        $recipientFlag = $chat->user_id_one == $recipientId ? $chat->user_one_flag : $chat->user_two_flag;

        if ($recipient && $recipientFlag !== 'spam') {
            $titles = [
                'en' => __('messages.new_message_title', [], 'en'),
                'ar' => __('messages.new_message_title', [], 'ar'),
            ];

            $messages = [
                'en' => __('messages.new_message_message', ['sender_name' => auth()->user()->username], 'en'),
                'ar' => __('messages.new_message_message', ['sender_name' => auth()->user()->username], 'ar'),
            ];

            // ✅ Use NoticeService
            $this->noticeService->send(
                $recipient->id,
                $titles,
                $messages,
                'chat',
                $chatId,
                false // broadcast unread count since this is a real actionable notification
            );
        }


        return response()->json([
            'message' => [
                'id'              => $message->id,
                'message'         => $message->message,
                'attachment_url'  => $message->attachment_url ? asset($message->attachment_url) : null,
                'attachment_type' => $message->attachment_type,
                'sender_id'       => $message->sender_id,
                'created_at'      => $message->created_at->toDateTimeString(),
            ]
        ]);
    }

    // public function sendMessage(Request $request, $chatId)
    // {
    //     $request->validate([
    //         'message' => 'nullable|string|max:1000',
    //         'attachment' => 'nullable|file|max:51200', // 50MB max

    //     ]);

    //     $message = new ChatMessage();
    //     $message->chat_id = $chatId;
    //     $message->sender_id = auth()->id();
    //     $message->message = $request->message;

    //     if ($request->hasFile('attachment')) {
    //         $file = $request->file('attachment');
    //         $path = FileManager::upload('chat_attachments', $file);

    //         $mimeType = $file->getMimeType();
    //         $type = 'file'; // default value

    //         // detect type
    //         if (str_starts_with($mimeType, 'image/')) {
    //             $type = 'image';
    //         } elseif (str_starts_with($mimeType, 'video/')) {
    //             $type = 'video';
    //         } elseif (str_starts_with($mimeType, 'audio/')) {
    //             $type = 'audio';
    //         } // for other call or specific file types, you can expand this

    //         $message->attachment_url = $path;
    //         $message->attachment_type = $type;

    //         // \Log::info('New message received:', ['type' => $file->getMimeType()]);
    //     }

    //     if ($request->hasFile('audio')) {
    //         $file = $request->file('audio');
    //         $path = FileManager::upload('chat_attachments', $file);

    //         $message->attachment_url = $path;
    //         $message->attachment_type = 'audio';
    //     }

    //     $message->save();

    //     // Broadcast to others
    //     broadcast(new PusherNewMessage($message))->toOthers();


    //     $chat = Chat::where('id',  $chatId)->first();

    //     if (!$chat) {
    //         return $this->errorResponse('Chat not found.', 404);
    //     }

    //     $authId = auth()->id();

    //     // Check which one is the other user
    //     $otherUserId = $chat->user_id_one == $authId ? $chat->user_id_two : $chat->user_id_one;

    //     // get the user details
    //     $user = User::find($otherUserId);

    //     // one signal notification*****************************************
    //     if ($user) {
    //         $playerIdRecord = PlayerId::where('user_id', $user->id)
    //             ->where('is_notifiable', 1)
    //             ->pluck('player_id')->toArray();


    //         if ($playerIdRecord) {
    //             $titles = [
    //                 'en' => __('messages.new_message_title', [], 'en'),
    //                 'ar' => __('messages.new_message_title', [], 'ar'),
    //             ];

    //             $messages = [
    //                 'en' => __('messages.new_message_message', ['sender_name' => $user->username], 'en'),
    //                 'ar' => __('messages.new_message_message', ['sender_name' => $user->username], 'ar'),
    //             ];

    //             $response = app(OneSignalService::class)->sendNotificationToUser(
    //                 $playerIdRecord,
    //                 $titles,
    //                 $messages,
    //                 'chat',
    //                 $request->chat_id
    //             );

    //             Notification::create([
    //                 'user_id'           => $user->id,
    //                 'title'             => json_encode($titles),
    //                 'body'              => json_encode($messages),
    //                 'type'              => 'chat',
    //                 'type_id'           => $request->chat_id,
    //                 'is_read'           => false,
    //                 'onesignal_id'      => $response['id'] ?? null,
    //                 'response_onesignal' => json_encode($response),
    //             ]);
    //         }
    //     }
    //     // *********************************************//
    //     return response()->json([
    //         'message' => [
    //             'id' => $message->id,
    //             'message' => $message->message,
    //             'attachment_url' => $message->attachment_url ? asset($message->attachment_url) : null,
    //             'attachment_type' => $message->attachment_type,
    //             'sender_id' => $message->sender_id,
    //             'created_at' => $message->created_at->toDateTimeString()
    //         ]
    //     ]);
    // }
}
