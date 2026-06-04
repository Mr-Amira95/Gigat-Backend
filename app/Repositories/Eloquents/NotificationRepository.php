<?php

namespace App\Repositories\Eloquents;

use App\Models\Notification;
use App\Traits\PaginateTrait;

use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    use PaginateTrait;
    protected $model;

    public function __construct(Notification $notification)
    {
        $this->model = $notification;
    }
    public function getNotificationsForUser($userId = null, $perPage = null)
    {

        // $query = $this->model->where('user_id', $userId)->orderBy('created_at', 'desc');;
        $this->model
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $query = $this->model
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        return $this->paginate($query, $perPage);
    }


    public function markAsRead($notificationId, $userId)
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
    }


    // public function markAsRead($userId)
    // {
    //     return Notification::where('user_id', $userId)
    //         ->where('is_read', false)
    //         ->update(['is_read' => true]);
    // }
    public function getUnreadNotifications()
    {
        $userId = auth('api')->id();
        return Notification::where(['is_read' => false, 'user_id' => $userId])->count();
    }
}
