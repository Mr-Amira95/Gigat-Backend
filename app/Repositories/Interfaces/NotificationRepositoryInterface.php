<?php

namespace App\Repositories\Interfaces;

interface NotificationRepositoryInterface
{
    public function getNotificationsForUser($userId);
    public function markAsRead($notificationId, $userId);
    public function getUnreadNotifications();
}
