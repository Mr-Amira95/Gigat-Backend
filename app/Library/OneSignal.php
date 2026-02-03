<?php

namespace App\Library;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\DeviceToken;

class OneSignal
{
    protected static $appId;
    protected static $apiKey;
    protected static $defaultIcon = '';

    public static function init()
    {
        self::$appId = config('onesignal.app_id');
        self::$apiKey = config('onesignal.api_key');
        self::$defaultIcon = '';
    }

    public static function sendNotification(array $data)
    {
        self::init();

        $playerIds = [];
        if (empty($data['is_general']) && !empty($data['user_ids'])) {
            $playerIds = DeviceToken::whereIn('user_id', $data['user_ids'])
                ->pluck('device_token')
                ->toArray();
        }

        try {
            $client = new Client();
            $response = $client->post('https://onesignal.com/api/v1/notifications', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . self::$apiKey,
                ],
                'json' => [
                    'app_id' => self::$appId,
                    'included_segments' => !empty($data['is_general']) ? ['All'] : [],
                    'include_player_ids' => !empty($playerIds) ? $playerIds : ($data['include_player_ids'] ?? null),
                    'data' => $data['data'] ?? [],
                    'headings' => [
                        'en' => $data['title']['en'],
                        'ar' => $data['title']['ar'],
                    ],
                    'contents' => [
                        'en' => $data['description']['en'],
                        'ar' => $data['description']['ar'],
                    ],
                    'small_icon' => self::$defaultIcon,
                    'large_icon' => $data['icon'] ?? self::$defaultIcon,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

//     $response = OneSignal::sendNotification([
//     'user_ids' => [1, 2, 3],
//     'title' => [
//         'en' => 'New Alert!',
//         'ar' => 'تنبيه جديد!',
//     ],
//     'message' => [
//         'en' => 'You have a new notification.',
//         'ar' => 'لديك إشعار جديد.',
//     ],
//     'icon' => '',
//     'data' => [
//         'type' => 'order_update',
//         'order_id' => 12345
//     ]
// ]);
}
