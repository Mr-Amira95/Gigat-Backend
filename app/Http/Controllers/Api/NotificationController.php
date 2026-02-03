<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;
use App\Models\PlayerId;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all general notifications and user-specific notifications.
     */
    public function getNotifications(Request $request)
    {
        $perPage = request('per_page', null);
        $userId = Auth::guard('api')->id();

        $notifications = $this->notificationService->getUserNotifications($userId, $perPage);

        return $this->successResponse(__('notifications_retrieved'), [
            'notifications' => NotificationResource::collection($notifications['data']),
            'meta' => $notifications['meta'],
        ]);
    }



    /**
     * Mark a notification as read.
     */
    // public function markAsRead()
    // {
    //     $userId = Auth::id();

    //     $result = $this->notificationService->markNotificationAsRead($userId);

    //         return $this->successResponse(__('notification_marked_as_read'));
    // }

    public function markAsReadByNotification($notificationId)
    {
        $result = $this->notificationService->markNotificationAsRead($notificationId);

        return $this->successResponse(__('notification_marked_as_read'));
    }

    public function changeNotifiable(Request $request)
    {
        $request->validate([
            'player_id'     => 'required|string',
        ]);

        $userId = Auth::guard('api')->id();

        // تأكيد وجود player_id مرتبط بالمستخدم الحالي
        $player = PlayerId::where('user_id', $userId)
            ->where('player_id', $request->player_id)
            ->first();

        if (!$player) {
            return response()->json([
                'status'  => false,
                'message' => 'Player ID not found for this user.',
            ], 404);
        }

        // تحديث الحالة
        $player->update([
            'is_notifiable' => $player->is_notifiable ? 0 : 1,
        ]);

        return $this->successResponse(__('notification_status_updated'), [
            'player' => $player,
        ]);
    }
}
