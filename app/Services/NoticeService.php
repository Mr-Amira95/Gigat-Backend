<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\PlayerId;
use App\Events\NewNotificationCount;
use App\Models\User;

class NoticeService
{
    protected OneSignalService $oneSignal;

    public function __construct(OneSignalService $oneSignal)
    {
        $this->oneSignal = $oneSignal;
    }

    public function send(
        $userIds,
        array $titles,
        array $messages,
        string $type,
        ?int $typeId = null,
        bool $broadcastCount = true
    ): void {

        $userIds = is_array($userIds) ? $userIds : [$userIds];

        foreach ($userIds as $uid) {

            // ✅ Calculate unread count PER USER
            $unreadCount = Notification::where('user_id', $uid)
                ->where('is_read', false)
                ->count();
            // dd($unreadCount);
            // Get player IDs PER USER
            $playerIds = PlayerId::where('user_id', $uid)
                ->where('is_notifiable', 1)
                ->pluck('player_id')
                ->toArray();

            $response = null;

            // ✅ Send push with badge count
            if (!empty($playerIds)) {
                $response = $this->oneSignal->sendNotificationToUser(
                    $playerIds,
                    $titles,
                    $messages,
                    $type,
                    $typeId,
                    $unreadCount
                );
            }

            // Store notification
            Notification::create([
                'user_id'            => $uid,
                'title'              => json_encode($titles),
                'body'               => json_encode($messages),
                'type'               => $type,
                'type_id'            => $typeId,
                'is_read'            => false,
                'onesignal_id'       => $response['id'] ?? null,
                'response_onesignal' => json_encode($response),
            ]);

            // Broadcast unread count
            if ($broadcastCount) {
                broadcast(new NewNotificationCount($uid, $unreadCount));
            }
        }
    }

    // public function send(
    //     $userIds,
    //     array $titles,
    //     array $messages,
    //     string $type,
    //     ?int $typeId = null,
    //     bool $broadcastCount = true
    // ): void {
    //     $userIds = is_array($userIds) ? $userIds : [$userIds];

    //     // get player ids
    //     $playerIds = PlayerId::whereIn('user_id', $userIds)
    //         ->where('is_notifiable', 1)
    //         ->pluck('player_id')
    //         ->toArray();

    //     $response = null;
    //     // dd($playerIds );

    //     if (!empty($playerIds)) {
    //         // calculate unread count ONCE
    //         $unreadCount = Notification::whereIn('user_id', $userIds)
    //             ->where('is_read', false)
    //             ->count();
    //         $response = $this->oneSignal->sendNotificationToUser(
    //             $playerIds,
    //             $titles,
    //             $messages,
    //             $type,
    //             $typeId,
    //             $unreadCount
    //         );
    //     }

    //     // store in DB
    //     foreach ($userIds as $uid) {
    //         $notification = Notification::create([
    //             'user_id'            => $uid,
    //             'title'              => json_encode($titles),
    //             'body'               => json_encode($messages),
    //             'type'               => $type,
    //             'type_id'            => $typeId,
    //             'is_read'            => false,
    //             'onesignal_id'       => $response['id'] ?? null,
    //             'response_onesignal' => json_encode($response),
    //         ]);

    //         if ($broadcastCount) {
    //             // $unreadCount = Notification::where('user_id', $uid)
    //             //     ->where('is_read', false)
    //             //     ->count();

    //             broadcast(new NewNotificationCount($uid, $unreadCount));
    //         }
    //     }
    // }
}
