<?php

namespace App\Http\Controllers\Api;

use App\Events\NewMessageEvent;
use App\Events\NewNotificationCount;
use App\Events\PusherNewMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatMessageResource;
use App\Models\Block;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\PlayerId;
use App\Models\User;
use App\Services\NoticeService;
use App\Services\OneSignalService;
use App\Utilities\FileManager;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    protected $noticeService;

    public function __construct(NoticeService $noticeService)
    {
        $this->noticeService    = $noticeService;
    }
    public function getAllChats()
    {
        $userId = auth()->id();

        $chats = Chat::with(['userOne', 'userTwo'])
            ->where(function ($q) use ($userId) {
                $q->where('user_id_one', $userId)
                    ->orWhere('user_id_two', $userId);
            })
            ->orderBy('updated_at', 'desc')
            ->get()
            ->filter(function ($chat) use ($userId) {
                // Exclude deleted chats for this user
                if ($chat->user_id_one == $userId && $chat->user_one_flag === 'deleted') {
                    return false;
                }
                if ($chat->user_id_two == $userId && $chat->user_two_flag === 'deleted') {
                    return false;
                }
                return true;
            })
            ->map(function ($chat) use ($userId) {
                $otherUser = $chat->user_id_one == $userId ? $chat->userTwo : $chat->userOne;

                $unreadCount = ChatMessage::where('chat_id', $chat->id)
                    ->where('sender_id', '!=', $userId)
                    ->where('is_read', false)
                    ->count();

                $lastMessage = ChatMessage::where('chat_id', $chat->id)
                    ->latest('created_at')
                    ->first();

                $blockedByMe = Block::where('blocker_id', $userId)
                    ->where('blocked_id', $otherUser->id)
                    ->exists();

                return [
                    'chat_id'       => $chat->id,
                    'chat_status' => $chat->user_id_one == $userId ? $chat->user_one_flag : $chat->user_two_flag,
                    'block_status' => $blockedByMe ? 'blocked' : 'unblocked',

                    // 'receiver'      => [
                    //     'id'       => $otherUser->id,
                    //     'username' => $otherUser->username,
                    //     'image'    => url($otherUser->avatar),
                    // ],
                    'receiver' => $otherUser->getChatProfile(),

                    'last_message'  => $lastMessage ? new ChatMessageResource($lastMessage) : null,
                    'unread_count'  => $unreadCount,
                ];
            });

        return $this->successResponse(__('messages.chats_retrived'), $chats->values());
    }



    public function startChat(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userIdOne = auth()->id();
        $userIdTwo = $validated['user_id'];

        $ids = [$userIdOne, $userIdTwo];
        sort($ids);

        $chat = Chat::firstOrCreate([
            'user_id_one' => (int)$ids[0],
            'user_id_two' => (int)$ids[1],
        ], [
            'user_one_flag' => 'normal',
            'user_two_flag' => 'normal',
        ]);

        $chat->created_at->toDayDateTimeString();

        return $this->successResponse(__('messages.chat_started'), $chat);
    }


    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id'          => 'required|exists:chats,id',
            'message'          => 'nullable|string',
            'attachment_file'  => 'nullable|file|mimes:jpg,jpeg,png,mp4,mp3,pdf,m4a',
            'attachment_type'  => 'nullable|in:image,video,file,audio|required_with:attachment_file',
        ]);

        if (is_null($request->message) && is_null($request->file('attachment_file'))) {
            return $this->errorResponse(__('either_message_or_attachment_required'));
        }

        $authUser = auth()->user();

        // Upload if file exists
        $attachmentUrl = $request->hasFile('attachment_file')
            ? FileManager::upload('chat_attachments', $request->file('attachment_file'))
            : null;

        // Create message
        $message = ChatMessage::create([
            'chat_id'         => $request->chat_id,
            'sender_id'       => $authUser->id,
            'message'         => $request->message,
            'attachment_url'  => $attachmentUrl,
            'attachment_type' => $request->attachment_type,
            'is_read'         => false,
        ]);

        $m = new ChatMessageResource($message);

        broadcast(new PusherNewMessage($m))->toOthers();
        // Load chat once
        $chat = Chat::findOrFail($request->chat_id);

        if ($chat->user_one_flag === 'deleted') {
            $chat->user_one_flag = 'normal';
        }
        if ($chat->user_two_flag === 'deleted') {
            $chat->user_two_flag = 'normal';
        }
        $chat->touch();
        $chat->save();

        // Find recipient
        $recipientId = $chat->user_id_one == $authUser->id ? $chat->user_id_two : $chat->user_id_one;
        $recipient   = User::find($recipientId);

        // Skip notification if recipient marked chat as spam
        $recipientFlag = $chat->user_id_one == $recipientId ? $chat->user_one_flag : $chat->user_two_flag;

        if ($recipient && $recipientFlag !== 'spam') {
            $titles = [
                'en' => __('messages.new_message_title', [], 'en'),
                'ar' => __('messages.new_message_title', [], 'ar'),
            ];

            $messages = [
                'en' => __('messages.new_message_message', ['sender_name' => $authUser->username], 'en'),
                'ar' => __('messages.new_message_message', ['sender_name' => $authUser->username], 'ar'),
            ];

            // ✅ Use NoticeService
            $this->noticeService->send(
                $recipient->id,
                $titles,
                $messages,
                'chat',
                $chat->id,
                true
            );
        }

        return $this->successResponse(__('messages.message_sent'), new ChatMessageResource($message));
    }


    public function getMessages($chatId)
    {

        $chat = Chat::with(['userOne:id,username,avatar', 'userTwo:id,username,avatar'])
            ->findOrFail($chatId);

        $currentUserId = auth()->user()->id;

        $messagesQuery = ChatMessage::where('chat_id', $chatId)
            ->with([
                'sender:id,username,avatar',
                'chat.userOne:id,username,avatar',
                'chat.userTwo:id,username,avatar'
            ]);

        // Apply deleted_at filter per user
        if ($chat->user_id_one == $currentUserId && $chat->user_one_deleted_at) {
            $messagesQuery->where('created_at', '>', $chat->user_one_deleted_at);
        }

        if ($chat->user_id_two == $currentUserId && $chat->user_two_deleted_at) {
            $messagesQuery->where('created_at', '>', $chat->user_two_deleted_at);
        }

        // Get filtered messages
        $messages = $messagesQuery->orderBy('created_at')->get();

        // Sender/Receiver
        $sender = $chat->user_id_one == $currentUserId ? $chat->userOne : $chat->userTwo;
        $receiver = $chat->user_id_one == $currentUserId ? $chat->userTwo : $chat->userOne;

        return $this->successResponse(__('messages.message_retrived'), [
            'sender' => $sender->getChatProfile(),
            'receiver' => $receiver->getChatProfile(),

            // 'sender' => [
            //     'id'       => $sender->id,
            //     'username' => $sender->username,
            //     'avatar'   => url($sender->avatar),
            // ],
            // 'receiver' => [
            //     'id'       => $receiver->id,
            //     'username' => $receiver->username,
            //     'avatar'   => url($receiver->avatar),
            // ],
            'messages' => ChatMessageResource::collection($messages),
        ]);
    }


    public function markAsRead($chatId)
    {
        ChatMessage::where('chat_id', $chatId)
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->successResponse(_('messages.mark_as_read_message'));
    }


    public function unreadCount($chatId)
    {
        $count = ChatMessage::where('chat_id', $chatId)
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->count();


        return $this->successResponse(_('messages.count_message'), $count);
    }

    public function toggleFlag(Request $request)
    {
        $userId = auth()->id();

        $request->validate([
            'flag' => 'required',
            'chat_id' => 'required',
        ]);

        $chat = Chat::where('id', $request->chat_id)->first();

        if ($chat->user_id_one == $userId) {
            $chat->user_one_flag = $request->flag;
            // if user sets flag to deleted, record the time
            if ($request->flag === 'deleted') {
                $chat->user_one_deleted_at = now();
            }

            $chat->save();
            return $this->successResponse(__('flag_updated_user_one'));
        }

        if ($chat->user_id_two == $userId) {
            $chat->user_two_flag = $request->flag;
            // if user sets flag to deleted, record the time
            if ($request->flag === 'deleted') {
                $chat->user_two_deleted_at = now();
            }
            $chat->save();
            return $this->successResponse(__('flag_updated_user_two'));
        }
        return $this->errorResponse(__('unauthorized_action'));
    }
}
