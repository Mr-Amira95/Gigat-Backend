<?php

namespace App\Services;

use Berkayk\OneSignal\OneSignalFacade as OneSignal;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    /**
     * Send a push notification to a specific user by player ID.
     *
     * @param string $playerId
     * @param string $title
     * @param string $message
     * @return void
     */

    public function sendNotificationToUser(array $playerId, array $titles, array $messages, $type, $typeId, int $badgeCount = 0)
    {
        try {
            $response = OneSignal::sendNotificationCustom([
                'include_player_ids' => $playerId,
                'headings' => [
                    'en' => $titles['en'],
                    'ar' => $titles['ar'],
                ],
                'contents' => [
                    'en' => $messages['en'],
                    'ar' => $messages['ar'],
                ],
                // ✅ BADGES
                'ios_badgeType'   => 'SetTo',
                'ios_badgeCount'  => $badgeCount,

                'android_badgeType'  => 'SetTo',
                'android_badgeCount' => $badgeCount,

                'data' => [
                    'type'    => $type,
                    'type_id' => $typeId,
                ],
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            // Log::info('OneSignal response', [
            //     'response' => $body
            // ]);
            return $data ?? [];
        } catch (\Throwable $e) {
            Log::error('OneSignal sendNotificationToUser failed', [
                'error' => $e->getMessage(),
                'type'  => $type,
                'type_id' => $typeId,
                'player_ids' => $playerId,
            ]);
        }
    }



    public function sendNotificationToUserCall(array $playerId, array $titles, array $messages, $type, $typeId)
    {
        $response = OneSignal::sendNotificationCustom([
            'include_player_ids' => $playerId,
            'headings' => [
                'en' => $titles['en'],
                'ar' => $titles['ar'],
            ],
            'contents' => [
                'en' => $messages['en'],
                'ar' => $messages['ar'],
            ],
            'data' => [
                'type'    => $type,
                'type_id' => $typeId,
            ],
            'ios_sound'   => 'ringtone.caf',  // for iOS
            'android_sound' => 'ringtone',    // for Android (without extension)
            'buttons' => [
                [
                    'id'    => 'accept_button',
                    'text'  => 'Accept',
                    'icon'  => 'ic_menu_check'
                ],
                [
                    'id'    => 'decline_button',
                    'text'  => 'Decline',
                    'icon'  => 'ic_menu_close_clear_cancel'
                ],
            ],
            // 'web_buttons' => [ // ⬅️ استخدم web_buttons بدل buttons
            //     [
            //         'id'    => 'accept_button',
            //         'text'  => 'Accept',
            //         'icon'  => 'ic_menu_check'
            //     ],
            //     [
            //         'id'    => 'decline_button',
            //         'text'  => 'Decline',
            //         'icon'  => 'ic_menu_close_clear_cancel'
            //     ],
            // ],
        ]);

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);
        return $data;
    }




    /**
     * Send a push notification to all users.
     *
     * @param string $title
     * @param string $message
     * @return void
     */
    // public function sendNotificationToAll(string $title, string $message): void
    // {
    //     OneSignal::sendNotificationCustom([
    //         'included_segments' => ['All'],
    //         'headings' => ['en' => (string)$title],
    //         'contents' => ['en' => (string)$message],
    //     ]);
    // }
}
