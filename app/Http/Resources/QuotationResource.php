<?php

namespace App\Http\Resources;

use App\Models\QuotationComment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Utilities\CurrencyConverter;
use App\Models\Currency;

class QuotationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $disabledComment = QuotationComment::where('user_id', Auth::id())
            ->where('quotation_id', $this->id)
            ->exists();
        // $comments = QuotationComment::with('user.profession')
        //     ->where('quotation_id', $this->id)
        //     ->get();
        // -------------------------------------------------
        // COMMENT FILTERING BASED ON USER TYPE (freelancer)
        // -------------------------------------------------
        $authUser = Auth::user();
        $isFreelancer = $authUser && $authUser->freelancer;

        $commentsQuery = QuotationComment::with('user.profession')
            ->where('quotation_id', $this->id);

        if ($isFreelancer) {
            $commentsQuery->where('user_id', $authUser->id);
        }
        $comments = $commentsQuery->get();
        // -------------------------------------------------
        // dd($comment);
        $currencyCode = $request->header('currency', 'USD');
        $currencyModel = Currency::where('code', strtoupper($currencyCode))->first();
        $symbol = $currencyModel ? $currencyModel->symbol : '$';
        $convertedPrice = $this->price
            ? CurrencyConverter::convert($this->price, 'USD', $currencyCode)
            : null;

        return [
            'id' => $this->id,
            'title' => $this->translation->title,
            'description' => $this->translation->description,
            // 'price' => $convertedPrice ? number_format((float)$convertedPrice, 2) . $symbol  : null,
            'price' => $convertedPrice !== null
                ? number_format((float) str_replace(',', '', $convertedPrice), 2) . ' ' . $symbol
                : null,

            'delivery_day' => $this->delivery_day,
            'revisions' => $this->revisions,
            'source_file' => (bool) $this->source_file,
            'attachments' => QuotationAttachmentResource::collection($this->whenLoaded('attachments')),
            'user' => $this->user ? [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'avatar' => $this->user->avatar ? url($this->user->avatar) : null,
                'profession' => optional(optional($this->user->profession)->translation)->title,
            ] : null,
            'created_at' => Carbon::parse($this->created_at)->toDayDateTimeString(),
            'updated_at' => Carbon::parse($this->updated_at)->toDayDateTimeString(),
            'disabled_comment' => $disabledComment,
            'comments' => QuotationCommentResource::collection($comments),
        ];
    }
}
