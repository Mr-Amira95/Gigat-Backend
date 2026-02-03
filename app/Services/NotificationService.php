<?php

namespace App\Services;

use App\Repositories\Interfaces\NotificationRepositoryInterface;

class NotificationService
{
    protected $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * Get all general notifications and user-specific notifications.
     */
    public function getUserNotifications($userId, $perPage = null)
    {
        return $this->notificationRepository->getNotificationsForUser($userId, $perPage);
    }

    /**
     * Mark a notification as read
     */
    // public function markNotificationAsRead($userId)
    // {
    //     return $this->notificationRepository->markAsRead($userId);
    // }
    public function getUnreadNotifications()
    {
        return $this->notificationRepository->getUnreadNotifications();
    }

    public function markNotificationAsRead($userId)
    {
        return $this->notificationRepository->markAsRead($userId);
    }
}
