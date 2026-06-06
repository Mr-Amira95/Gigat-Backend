<?php

namespace App\Services;

use Exception;
use App\Models\Visitor;
use App\Models\AnalyticsEvent;

class AnalyticsService
{
    /**
     * Create or update a visitor record (upsert by visitor_uuid).
     */
    public function upsertVisitor(array $data): array
    {
        try {
            $visitor = Visitor::updateOrCreate(
                ['visitor_uuid' => $data['visitor_uuid']],
                array_filter([
                    'platform'       => $data['platform'] ?? null,
                    'device_type'    => $data['device_type'] ?? null,
                    'device_os'      => $data['device_os'] ?? null,
                    'device_browser' => $data['device_browser'] ?? null,
                    'device_model'   => $data['device_model'] ?? null,
                    'country'  => $data['country'] ?? null,
                    'user_id'  => $data['user_id'] ?? null,
                    'ip_address'     => $data['ip_address'] ?? null,
                ], fn($v) => !\is_null($v))
            );

            return ['visitor' => $visitor, 'created' => $visitor->wasRecentlyCreated];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Record an analytics event.
     */
    public function recordEvent(array $data): AnalyticsEvent
    {
        try {
            $visitor = Visitor::where('visitor_uuid', $data['visitor_uuid'])->firstOrFail();

            $userId = $data['user_id'] ?? null;

            // If a user is identified and the visitor isn't linked yet, link them now
            if ($userId && \is_null($visitor->user_id)) {
                $visitor->user_id = $userId;
                $visitor->save();
            }

            return AnalyticsEvent::create([
                'visitor_id'  => $visitor->getKey(),
                'user_id'     => $userId,
                'event_name'  => $data['event_name'],
                'screen_name' => $data['screen_name'] ?? null,
                'metadata'    => $data['metadata'] ?? null,
                'created_at'  => now(),
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
