<?php

namespace App\Repositories\Interfaces;

interface NotificationRepositoryInterface
{
    public function getNotificationsForUser($userId);
    public function markAsRead($userId);
    public function getUnreadNotifications();
}
