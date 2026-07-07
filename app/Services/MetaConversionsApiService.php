<?php

namespace App\Services;

use App\Jobs\SendMetaConversionEventJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MetaConversionsApiService
{
    protected ?string $datasetId;
    protected ?string $accessToken;
    protected string $apiVersion;
    protected ?string $testEventCode;

    public function __construct()
    {
        $this->datasetId     = config('services.meta.dataset_id');
        $this->accessToken   = config('services.meta.access_token');
        $this->apiVersion    = config('services.meta.api_version', 'v21.0');
        $this->testEventCode = config('services.meta.test_event_code');
    }

    /**
     * Build the event payload and queue it for delivery.
     */
    public function dispatchEvent(User $user, Request $request, string $eventName): void
    {
        SendMetaConversionEventJob::dispatch(
            $this->buildEventData($user, $request, $eventName)
        );
    }

    public function buildEventData(User $user, Request $request, string $eventName): array
    {
        $userData = array_filter([
            'em'                 => $user->email ? $this->hash($user->email) : null,
            'ph'                 => $user->phone ? $this->hash($this->digitsOnly($user->full_phone)) : null,
            'external_id'        => $this->hash((string) $user->id),
            'client_ip_address'  => $request->ip(),
            'client_user_agent'  => $request->userAgent(),
            'fbp'                => $request->cookie('_fbp'),
            'fbc'                => $request->cookie('_fbc'),
        ]);

        return array_filter([
            'event_name'       => $eventName,
            'event_time'       => now()->timestamp,
            'event_id'         => (string) Str::uuid(),
            'action_source'    => 'website',
            'event_source_url' => $request->headers->get('referer') ?: $request->fullUrl(),
            'user_data'        => $userData,
        ]);
    }

    public function postEvent(array $eventData): void
    {
        if (!$this->datasetId || !$this->accessToken) {
            Log::error('MetaConversionsApiService: missing dataset_id or access_token, event not sent', [
                'event_name' => $eventData['event_name'] ?? null,
            ]);
            return;
        }

        $payload = [
            'data'         => json_encode([$eventData]),
            'access_token' => $this->accessToken,
        ];

        if ($this->testEventCode) {
            $payload['test_event_code'] = $this->testEventCode;
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->datasetId}/events";

        $response = Http::asForm()->post($url, $payload);

        if ($response->successful()) {
            Log::info('MetaConversionsApiService: event sent', [
                'event_name' => $eventData['event_name'] ?? null,
                'response'   => $response->json(),
            ]);
        } else {
            Log::error('MetaConversionsApiService: failed to send event', [
                'event_name' => $eventData['event_name'] ?? null,
                'status'     => $response->status(),
                'response'   => $response->json(),
            ]);
        }
    }

    protected function hash(string $value): string
    {
        return hash('sha256', strtolower(trim($value)));
    }

    protected function digitsOnly(string $value): string
    {
        return preg_replace('/\D+/', '', $value);
    }
}
