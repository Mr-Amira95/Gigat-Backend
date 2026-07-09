<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Meta calls this with GET once, when you register the webhook URL,
     * to confirm you control the endpoint.
     */
    public function verify(Request $request)
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $expectedToken = config('services.whatsapp.webhook_verify_token');

        Log::channel('whatsapp')->info('WhatsApp webhook verification attempt', [
            'mode'  => $mode,
            'match' => $expectedToken && hash_equals($expectedToken, (string) $token),
        ]);

        if ($mode === 'subscribe' && $expectedToken && hash_equals($expectedToken, (string) $token)) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * Meta calls this with POST for every message/status event
     * (sent, delivered, read, failed, inbound messages, etc).
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::channel('whatsapp')->info('WhatsApp webhook event received', [
            'payload' => json_encode($payload),
        ]);

        foreach (($payload['entry'] ?? []) as $entry) {
            foreach (($entry['changes'] ?? []) as $change) {
                $value = $change['value'] ?? [];

                foreach (($value['statuses'] ?? []) as $status) {
                    $context = [
                        'message_id'   => $status['id'] ?? null,
                        'status'       => $status['status'] ?? null,
                        'recipient_id' => $status['recipient_id'] ?? null,
                        'timestamp'    => $status['timestamp'] ?? null,
                        'errors'       => $status['errors'] ?? null,
                    ];

                    if (($status['status'] ?? null) === 'failed') {
                        Log::channel('whatsapp')->error('WhatsApp message status: failed', $context);
                    } else {
                        Log::channel('whatsapp')->info('WhatsApp message status update', $context);
                    }
                }

                foreach (($value['messages'] ?? []) as $message) {
                    Log::channel('whatsapp')->info('WhatsApp inbound message received', [
                        'from'    => $message['from'] ?? null,
                        'message' => $message,
                    ]);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
