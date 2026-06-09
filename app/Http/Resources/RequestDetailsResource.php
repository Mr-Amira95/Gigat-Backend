<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Enums\RequestStatusEnum;
use App\Models\Service;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\UserRating;

class RequestDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $service = $this->service; // already withTrashed()
        $freelancer = optional($service)->user;

        return [
            'id' => $this->id,
            'title' => $this->translation->title,
            'description' => $this->translation->description,
            'image_url' => $this->image ? url($this->image) : null,
            'created_at' => Carbon::parse($this->created_at)->toDayDateTimeString(),
            'created_since' => Carbon::parse($this->created_at)->diffForHumans(),
            'status_key' => $this->status,
            'status_label' => RequestStatusEnum::tryFrom($this->status)?->label(),
            'revisions_count' => $this->revisions_count,
            'contract_path' => url($this->contract_path),
            'client_payment_status' => optional($this->finance)->client_payment_status,
            // 'is_reviewed' => $this->service->reviews()->where('user_id', $this->user_id)->exists(),
            'freelancer_reviewed' => $this->service
                ? $this->service->reviews()
                ->where('user_id', $this->user_id)
                ->exists()
                : false,
            'client_reviewed' => UserRating::where('request_id', $request->id)
                ->where('rater_id', $service?->user_id)
                ->where('ratee_id', $this->user_id)
                ->exists(),
            'user' => new UserResource($this->user),
            'client' => new UserResource($this->user),
            'freelancer' => new UserResource($freelancer),
            'service' => new ServiceResource($this->service),
            // 'plan'  => new PlanResource($this->plan),       // current live plan (dynamic)
            'plan' => new RequestPlanResource($this),      // frozen record (request_features)
            'logs' => RequestLogResource::collection(
                $this->logs()->orderBy('created_at', 'desc')->get()
            ),
            'deliveries' => RequestDeliveryResource::collection(
                $this->deliveries()->with(['attachments'])->get()
            ),
            'feedbacks' => RequestFeedbackResource::collection(
                $this->feedbacks()->with(['attachments'])->get()
            ),

        ];
    }
}
