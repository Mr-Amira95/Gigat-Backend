<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $token;
    protected $phoneNumberId;
    protected $templateName;

    public function __construct()
    {
        $this->token        = config('services.whatsapp.token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->templateName  = config('services.whatsapp.template_name');
    }

    public function sendTemplateMessage($to, $otp, $user = null)
    {
        $url = "https://graph.facebook.com/v20.0/{$this->phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => [
                'name'       => $this->templateName,
                'language'   => ['code' => 'en_US'],  // or 'ar' or whatever your template language is
                'components' => [
                    [
                        'type'       => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $otp
                            ]
                        ]
                    ],
                    [
                        'type'       => 'button',
                        'sub_type'   => 'url',
                        'index'      => '0',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $otp
                            ]
                        ]
                    ]
                ]

            ],
        ];

        $userContext = $user ? [
            'user_id' => $user->id ?? null,
            'name'    => $user->name ?? null,
            'email'   => $user->email ?? null,
            'prefix'  => $user->prefix ?? null,
            'phone'   => $user->phone ?? null,
        ] : null;

        Log::channel('whatsapp')->info('WhatsApp OTP send attempt', [
            'to'              => $to,
            'otp'             => $otp,
            'phone_number_id' => $this->phoneNumberId,
            'template'        => $this->templateName,
            'token_set'       => !empty($this->token),
            'user'            => $userContext,
            'payload'         => $payload,
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type'  => 'application/json',
        ])->post($url, $payload);

        $logContext = [
            'to'     => $to,
            'user'   => $userContext,
            'status' => $response->status(),
            'body'   => $response->json(),
        ];

        if ($response->failed()) {
            Log::channel('whatsapp')->error('WhatsApp OTP send failed', $logContext);
        } else {
            Log::channel('whatsapp')->info('WhatsApp OTP send succeeded', $logContext);
        }

        return $response->json();
    }
}
