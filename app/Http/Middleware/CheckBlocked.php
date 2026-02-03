<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Utilities\BlockHelper;
use App\Models\Chat;

class CheckBlocked
{
    public function handle(Request $request, Closure $next)
    {
        $authId = auth('api')->id() ?? auth('freelancer')->id();
        $targetId = null;

        // Case 1: Chat routes (detect the other participant from chat_id)
        if ($request->has('chat_id') || $request->route('chatId')) {
            $chatId = $request->chat_id ?? $request->route('chatId');
            $chat = Chat::find($chatId);
            if ($chat && $authId) {
                $targetId = $chat->user_id_one == $authId
                    ? $chat->user_id_two
                    : $chat->user_id_one;
            }
        }

        // dd( $targetId);
        // Case 2: Fallback (profile APIs where user_id or userId is passed)
        if (!$targetId) {
            $targetId = $request->receiver_id
                ?? $request->input('user_id')
                ?? $request->route('userId')
                ?? $request->route('id');
        }

        // Block check
        if ($targetId && BlockHelper::isBlocked($authId, $targetId)) {
            return response()->json([
                'status'  => false,
                'message' => __('interaction_not_allowed'),
                'data'    => null,
            ], 403);
        }

        return $next($request);
    }
}
