<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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

    public function sendTemplateMessage($to, $otp)
    {
        $url = "https://graph.facebook.com/v20.0/{$this->phoneNumberId}/messages";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type'  => 'application/json',
        ])->post($url, [
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
        ]);

        return $response->json();
    }
}
