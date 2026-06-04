<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RequestCreateRequest;
use App\Models\PaymentSession;
use App\Models\Quotation;
use App\Models\QuotationComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }
    public function createCheckoutSession(RequestCreateRequest $request)
    {

        try {
            $validatedData = $request->validated();

            $checkoutController = app(CheckoutController::class);
            $response = $checkoutController->proceedCheckout($request);
            $serviceTitle = $response->getData(true)['data']['request_info']['title'];
            $formattedTotal = $response->getData(true)['data']['request_info']['original_total'];
            $amount = floatval(preg_replace('/[^0-9.]/', '', $formattedTotal));

            $type =
                !empty($validatedData['quotation_id']) ? 'quotation' : (!empty($validatedData['request_id']) ? 'request_payment' : 'service');
            // Prepare line items for Stripe
            $lineItems = [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $serviceTitle,
                            'metadata' => [
                                'user_id'      => auth()->id(),
                                'service_id'   => $validatedData['service_id'] ?? null,
                                'plan_id'      => $validatedData['plan_id'] ?? null,
                                'quotation_id' => $validatedData['quotation_id'] ?? null,
                                'comment_id'   => $validatedData['comment_id'] ?? null,
                                'type'         => $type,
                                // 'type'         => !empty($validatedData['quotation_id']) ? 'quotation' : 'service',
                                'client_payment_status'   => 'paid',
                                'request_id'   => $validatedData['request_id'] ?? null,
                            ],
                        ],

                        'unit_amount' => intval($amount * 100),
                    ],
                    'quantity' => 1,
                ]
            ];

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' =>  $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' =>  route('stripe.cancel'),
                // 'success_url' => 'https://b0dd-176-29-169-131.ngrok-free.app/stripe/success?session_id={CHECKOUT_SESSION_ID}',
                // 'cancel_url' =>  'https://b0dd-176-29-169-131.ngrok-free.app/stripe/cancel',
                'payment_intent_data' => [
                    'metadata' => [
                        'platform' => 'Gigat',
                    ],
                ],

            ]);

            return $this->successResponse(__('success'), ['url' => $session->url]);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function success(Request $request)
    {
        try {
            $sessionId = $request->query('session_id');

            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $url = config('app.frontend_url') . '/request-success';
                return redirect()->away($url);
            }
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function cancel()
    {
        try {
            $url = config('app.frontend_url') . '/request-reject';
            return redirect()->away($url);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            return $this->errorResponse('Invalid payload');
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return $this->errorResponse('Invalid signature');
        }

        // Handle checkout session completed
        if ($event->type === 'checkout.session.completed') {
            try {
                $session = $event->data->object;

                // Idempotency guard: skip if this session was already processed
                if (\App\Models\Finance::where('stripe_session_id', $session->id)->exists()) {
                    return response('Already processed', 200);
                }

                // Retrieve session with expanded line items to access metadata
                $session = Session::retrieve([
                    'id' => $session->id,
                    'expand' => ['line_items.data.price.product'],
                ]);

                $lineItem = $session->line_items->data[0] ?? null;
                $metadata = $lineItem->price->product->metadata ?? [];
                $data = is_object($metadata) ? $metadata->toArray() : (array)$metadata;
                $data['stripe_session_id'] = $session->id;

                // Check if payment is successful
                // if ($session->payment_status === 'paid') {

                //     if (($data['type'] ?? null) === 'quotation') {
                //         // ✅ Finalize quotation flow
                //         $quotationController = app(QuotationController::class);
                //         $quotation = Quotation::findOrFail($data['quotation_id']);
                //         $comment   = QuotationComment::findOrFail($data['comment_id']);
                //         $quotationController->finalizeQuotationRequest($quotation, $comment, $data);
                //     } else {
                //         // ✅ Normal service flow
                //         $requestController = app(RequestController::class);
                //         $requestController->createRequest($data);
                //     }

                //     Log::info('Webhook processing completed successfully', ['session_id' => $session->id]);
                //     return $this->successResponse('Webhook processed and request created');
                // }
                if ($session->payment_status === 'paid') {

                    $type = $data['type'] ?? null;

                    switch ($type) {

                        case 'quotation':

                            $quotationController = app(QuotationController::class);
                            $quotation = Quotation::findOrFail($data['quotation_id']);
                            $comment   = QuotationComment::findOrFail($data['comment_id']);
                            $quotationController->finalizeQuotationRequest($quotation, $comment, $data);

                            break;

                        case 'request_payment':

                            $request = \App\Models\Request::findOrFail($data['request_id']);

                            // 1️⃣ Update finance
                            $request->finance()->update([
                                'client_payment_status' => 'paid'
                            ]);

                            $request->update([
                                'status' => 'pending'
                            ]);

                            break;

                        case 'service':

                            $requestController = app(RequestController::class);
                            $requestController->createRequest($data);

                            break;

                        default:
                            Log::warning('Unknown payment type', ['type' => $type]);
                            break;
                    }

                    Log::info('Webhook processed successfully', [
                        'session_id' => $session->id,
                        'type'       => $type
                    ]);

                    return $this->successResponse('Webhook processed successfully');
                }

                Log::warning('Payment not completed', ['session_id' => $session->id]);
                return $this->errorResponse('Payment not completed');
            } catch (\Exception $e) {
                Log::error('Stripe webhook error in processing', ['error' => $e->getMessage()]);
                return $this->exceptionResponse($e);
            }
        }
        return $this->successResponse('Webhook received (no action taken)');
    }
}
