<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\QuotationCommentRequest;
use App\Http\Requests\Api\QuotationRequest;
use App\Http\Resources\QuotationCommentResource;
use App\Http\Resources\QuotationResource;
use App\Models\Block;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Freelancer;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\Quotation;
use App\Models\QuotationComment;
use App\Models\Request;
use App\Models\Service;
use App\Models\User;
use App\Services\MetaConversionsApiService;
use App\Services\NoticeService;
use App\Services\QuotationService;
use App\Services\RequestService;
use App\Utilities\CurrencyConverter;
use App\Utilities\FileManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session;

class QuotationController extends Controller
{
    protected $quotationService;

    protected $requestService;

    protected $noticeService;

    public function __construct(QuotationService $quotationService, RequestService $requestService, NoticeService $noticeService)
    {
        $this->quotationService = $quotationService;
        $this->requestService = $requestService;
        $this->noticeService = $noticeService;
    }

    public function getAll()
    {
        try {
            $perPage = request()->query('per_page');
            $quotations = $this->quotationService->getAllQuotations($perPage);

            return $this->successResponse(__('quotations_retrieved_successfully'), [
                'quotations' => QuotationResource::collection($quotations['data']),
                'meta' => $quotations['meta'],
            ]);

            return response()->json($quotations);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e, __('failed_to_retrieve_quotations'));
        }
    }

    public function getByUserId()
    {
        try {
            $perPage = request()->query('per_page');
            $quotations = $this->quotationService->getByUserId($perPage);

            return $this->successResponse(__('quotations_retrieved_successfully'), [
                'quotations' => QuotationResource::collection($quotations['data']),
                'meta' => $quotations['meta'],
            ]);

            return response()->json($quotations);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e, __('failed_to_retrieve_quotations'));
        }
    }

    public function createQuotation(QuotationRequest $request): JsonResponse
    {
        $currencyCode = $request->currency;
        $currencyModel = Currency::where('code', strtoupper($currencyCode))->first();
        $symbol = $currencyModel ? $currencyModel->symbol : '$';

        $userId = Auth::id();
        if (! $userId) {
            return $this->errorResponse(__('unauthorized'));
        }
        $data = array_merge($request->validated(), ['user_id' => $userId]);
        // $data['price'] = $data['price']
        //     ? CurrencyConverter::convert($data['price'], $currencyCode, 'USD')
        //     : null;
        $data['price'] = $data['price']
            ? (float) str_replace(',', '', CurrencyConverter::convert($data['price'], $currencyCode, 'USD'))
            : null;

        // 1️ Create Quotation
        $quotation = $this->quotationService->store($data);

        // Handle Attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filePath = FileManager::upload('quotations/attachments', $file);

                $quotation->attachments()->create([
                    'attachment_url' => $filePath,
                ]);
            }
        }

        // Notify freelancers
        $categoryId = $quotation->subcategory->category_id;
        $blockedIds = Block::where('blocker_id', $userId)->pluck('blocked_id')->toArray();
        $blockedByIds = Block::where('blocked_id', $userId)->pluck('blocker_id')->toArray();
        $allBlocked = array_merge($blockedIds, $blockedByIds);

        $users = User::whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        })
            ->when(! empty($allBlocked), function ($q) use ($allBlocked) {
                $q->whereNotIn('id', $allBlocked);
            })
            ->get();

        // one signal notification*****************************************
        if ($users) {
            $titles = [
                'en' => __('messages.new_job_quotation_title', [], 'en'),
                'ar' => __('messages.new_job_quotation_title', [], 'ar'),
            ];

            $messages = [
                'en' => __('messages.new_job_quotation_message', ['quotation_title' => $quotation->title], 'en'),
                'ar' => __('messages.new_job_quotation_message', ['quotation_title' => $quotation->title], 'ar'),
            ];

            $this->noticeService->send(
                $users->pluck('id')->toArray(),
                $titles,
                $messages,
                'quotation',
                $quotation->id
            );
        }

        // *********************************************//
        app(MetaConversionsApiService::class)->dispatchEvent(Auth::user(), $request, 'Client Create RFQ');

        return $this->successResponse(__('success'), new QuotationResource($quotation));
    }

    public function findById(int $id): JsonResponse
    {
        try {
            $quotation = $this->quotationService->getQuotationById($id);

            if (! $quotation) {
                return $this->errorResponse(__('quotation_not_found'), 404);
            }

            $user = Auth::user();

            if ($user) {
                // if user is a client
                if (! $user->freelancer && (int) $quotation->user_id !== (int) $user->id) {
                    return $this->errorResponse(__('unauthorized'), 403);
                }

                // if user is a freelancer
                if ($user->freelancer) {
                    $quotation->loadMissing('subCategory:id,category_id');

                    $hasCategory = $user->categories1()
                        ->where('categories.id', $quotation->subCategory->category_id ?? null)
                        ->exists();

                    if (! $hasCategory) {
                        return $this->errorResponse(__('unauthorized'), 403);
                    }
                }
            }

            return $this->successResponse(__('success'), new QuotationResource($quotation));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e, __('failed_to_retrieve_quotations'));
        }
    }

    public function createComment(QuotationCommentRequest $request): JsonResponse
    {
        $userId = Auth::id();

        if (! $userId) {
            return $this->errorResponse(__('unauthorized'));
        }

        $data = array_merge($request->validated(), ['user_id' => $userId]);

        $comment = $this->quotationService->createQuotationComment($data);

        // one signal notification*****************************************
        // ✅ Send notice via NoticeService
        $user = $comment->quotation->user;
        if ($user) {
            $titles = [
                'en' => __('messages.new_comment_title', [], 'en'),
                'ar' => __('messages.new_comment_title', [], 'ar'),
            ];

            $messages = [
                'en' => __('messages.new_comment_message', ['freelancer_name' => $comment->user->username], 'en'),
                'ar' => __('messages.new_comment_message', ['freelancer_name' => $comment->user->username], 'ar'),
            ];

            $this->noticeService->send(
                $user->id,
                $titles,
                $messages,
                'quotation',
                $comment->quotation_id
            );
        }

        // *********************************************//
        if (Auth::user()->freelancer) {
            app(MetaConversionsApiService::class)->dispatchEvent(Auth::user(), $request, 'Freelancer Comment on RFQ');
        }

        return $this->successResponse(__('success'), new QuotationCommentResource($comment));
    }

    public function getCommentsByQuotationId(int $quotationId): JsonResponse
    {
        try {
            $perPage = request()->query('per_page');
            $comments = $this->quotationService->getCommentsByQuotationId($quotationId, $perPage);

            return $this->successResponse(__('quotations_comments_retrieved_successfully'), QuotationCommentResource::collection($comments));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e, __('failed_to_retrieve_quotations_comments'));
        }
    }

    public function approveQuotation(Request $request, $id)
    {
        $comment = QuotationComment::findOrFail($id);
        $quotation = Quotation::findOrFail($comment->quotation_id);

        if ($quotation->user_id !== Auth::id()) {
            return $this->errorResponse(__('unauthorized'), 403);
        }

        return DB::transaction(function () use ($id, $comment, $quotation) {
            $category = Category::find($quotation->subCategory->category_id);

            $service = new Service();
            $service->sub_category_id = $quotation->sub_category_id;
            $service->user_id = $comment->user_id;
            $service->status = 'approved';
            $service->save();
            $service->translations()->create([
                'language' => 'en',
                'title' => $quotation->title,
                'description' => $quotation->description,
            ]);

            // Add Arabic translation
            $service->translations()->create([
                'language' => 'ar',
                'title' => $quotation->title,
                'description' => $quotation->description,
            ]);
            // price
            $feature_price = new PlanFeature();
            $feature_price->plan_id = 1;
            $feature_price->service_id = $service->id;
            $feature_price->value = $quotation->price;
            $feature_price->type = 'price';
            $feature_price->save();

            // delivery_days
            $feature_days = new PlanFeature();
            $feature_days->plan_id = 1;
            $feature_days->service_id = $service->id;
            $feature_days->value = $quotation->delivery_day;
            $feature_days->type = 'delivery_days';
            $feature_days->save();

            // revisions
            $feature_revisions = new PlanFeature();
            $feature_revisions->plan_id = 1;
            $feature_revisions->service_id = $service->id;
            $feature_revisions->value = $quotation->revisions;
            $feature_revisions->type = 'revisions';
            $feature_revisions->save();

            // source_files
            $feature_source = new PlanFeature();
            $feature_source->plan_id = 1;
            $feature_source->service_id = $service->id;
            $feature_source->value = $quotation->source_file;
            $feature_source->type = 'source_files';
            $feature_source->save();

            // Create translations for each feature
            foreach (['price', 'delivery_days', 'revisions', 'source_files'] as $type) {
                $feature = PlanFeature::where('service_id', $service->id)
                    ->where('type', $type)
                    ->first();

                if ($feature) {
                    $key = strtolower(str_replace(' ', '_', $type));

                    foreach (['en', 'ar'] as $locale) {
                        $titleFromLang = __("features.$key", locale: $locale);

                        $feature->translations()->updateOrCreate(
                            ['language' => $locale],
                            [
                                'title' => $titleFromLang !== "features.$key"
                                    ? $titleFromLang
                                    : ucfirst($type),
                            ]
                        );
                    }
                }
            }

            $data = [
                'service_id' => $service->id,
                'plan_id' => 1,
            ];

            // $request = $this->requestService->createRequest($data);
            $requestController = app(RequestController::class);
            $response = $requestController->createRequest($data);
            $service->delete();
            $quotation->delete();

            return $this->successResponse(__('success'), __('quotation_approved'));
        });
    }

    public function finalizeQuotationRequest(Quotation $quotation, QuotationComment $comment, array $data = [])
    {
        return DB::transaction(function () use ($quotation, $comment, $data) {
            // 1. Create temporary service from quotation
            $service = new Service();
            $service->sub_category_id = $quotation->sub_category_id;
            $service->user_id = $comment->user_id;
            $service->status = 'approved';
            $service->save();

            // 2. Add translations
            foreach ($quotation->translations as $translation) {
                $service->translations()->create([
                    'language'    => $translation->language,
                    'title'       => $translation->title,
                    'description' => $translation->description,
                ]);
            }


            // 3. Create plan features
            $features = [
                'price'         => $quotation->price,
                'delivery_days' => $quotation->delivery_day,
                'revisions'     => $quotation->revisions,
                'source_files'  => $quotation->source_file,
            ];

            foreach ($features as $type => $value) {
                $feature = new PlanFeature();
                $feature->plan_id = $data['plan_id'] ?? 1;   // fallback if not passed
                $feature->service_id = $service->id;
                $feature->value = $value;
                $feature->type = $type;
                $feature->save();

                // translations for each feature
                foreach (['en', 'ar'] as $locale) {
                    $titleFromLang = __("features.$type", locale: $locale);

                    $feature->translations()->updateOrCreate(
                        ['language' => $locale],
                        [
                            'title' => $titleFromLang !== "features.$type"
                                ? $titleFromLang
                                : ucfirst($type),
                        ]
                    );
                }
            }

            // 4. Create request from service
            $requestData = [
                'service_id'            => $service->id,
                'plan_id'               => $data['plan_id'] ?? 1,
                'user_id'               => $data['user_id'],
                'client_payment_status' => $data['client_payment_status'],
                'stripe_session_id'     => $data['stripe_session_id'] ?? null,
            ];
            $requestController = app(RequestController::class);
            $requestController->createRequest($requestData);


            // 5. Cleanup: remove service + quotation
            $service->delete();
            $quotation->delete();

            return $this->successResponse(__('success'), __('quotation_approved'));
        });
    }

    public function getByFreelancerId()
    {
        try {
            $perPage = request()->query('per_page');
            $quotations = $this->quotationService->getQuotationsForFreelancer($perPage);

            return $this->successResponse(__('quotations_retrieved_successfully'), [
                'quotations' => QuotationResource::collection($quotations),
                // 'meta' => $quotations['meta']
            ]);

            return response()->json($quotations);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e, __('failed_to_retrieve_quotations'));
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $userId = auth()->id();

            $quotation = $this->quotationService->getQuotationById($id);

            if (! $quotation) {
                return $this->errorResponse(__('quotation_not_found'), 404);
            }

            if ($quotation->user_id !== $userId) {
                return $this->errorResponse(__('unauthorized'), 403);
            }

            $this->quotationService->delete($id);

            return $this->successResponse(__('quotation_deleted_successfully'));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
