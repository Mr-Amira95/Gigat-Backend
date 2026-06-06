<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RequestCreateRequest;
use App\Models\PaymentSession;
use App\Models\Quotation;
use App\Models\QuotationComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            $expectedAmountCents = intval($amount * 100);

            $lineItems = [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $serviceTitle,
                            'metadata' => [
                                'user_id'               => auth()->id(),
                                'service_id'            => $validatedData['service_id'] ?? null,
                                'plan_id'               => $validatedData['plan_id'] ?? null,
                                'quotation_id'          => $validatedData['quotation_id'] ?? null,
                                'comment_id'            => $validatedData['comment_id'] ?? null,
                                'type'                  => $type,
                                'client_payment_status' => 'paid',
                                'request_id'            => $validatedData['request_id'] ?? null,
                                'expected_amount_cents' => $expectedAmountCents,
                            ],
                        ],

                        'unit_amount' => $expectedAmountCents,
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

            $session = Session::retrieve([
                'id'     => $sessionId,
                'expand' => ['line_items.data.price.product'],
            ]);

            if ($session->payment_status === 'paid') {
                $this->processPayment($session);
                $url = config('app.frontend_url') . '/request-success';
                return redirect()->away($url);
            }
        } catch (\Exception $e) {
            Log::error('Stripe success handler error', ['error' => $e->getMessage()]);
        }

        return redirect()->away(config('app.frontend_url') . '/request-success');
    }

    private function processPayment($session): void
    {
        $lock = \Illuminate\Support\Facades\Cache::lock('stripe_webhook_' . $session->id, 120);
        if (!$lock->get()) {
            return;
        }

        if (\App\Models\Finance::where('stripe_session_id', $session->id)->exists()) {
            $lock->release();
            return;
        }

        try {
            $lineItem = $session->line_items->data[0] ?? null;
            $metadata = $lineItem->price->product->metadata ?? [];
            $data = is_object($metadata) ? $metadata->toArray() : (array) $metadata;
            $data['stripe_session_id'] = $session->id;

            $type = $data['type'] ?? null;

            switch ($type) {
                case 'quotation':
                    $quotationController = app(QuotationController::class);
                    $quotation = Quotation::findOrFail($data['quotation_id']);
                    $comment   = QuotationComment::findOrFail($data['comment_id']);
                    $quotationController->finalizeQuotationRequest($quotation, $comment, $data);
                    break;

                case 'request_payment':
                    $req = \App\Models\Request::findOrFail($data['request_id']);
                    $req->finance()->update(['client_payment_status' => 'paid']);
                    $req->update(['status' => 'pending']);
                    break;

                case 'service':
                    $requestController = app(RequestController::class);
                    $requestController->createRequest($data);
                    break;

                default:
                    Log::warning('Stripe: unknown payment type', ['type' => $type]);
            }

            Log::info('Stripe payment processed', ['session_id' => $session->id, 'type' => $type]);
        } finally {
            $lock->release();
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

        if ($event->type === 'checkout.session.completed') {
            try {
                $session = $event->data->object;

                // Verify amount before processing
                $session = Session::retrieve([
                    'id'     => $session->id,
                    'expand' => ['line_items.data.price.product'],
                ]);

                $lineItem      = $session->line_items->data[0] ?? null;
                $metadata      = $lineItem->price->product->metadata ?? [];
                $data          = is_object($metadata) ? $metadata->toArray() : (array) $metadata;
                $expectedCents = isset($data['expected_amount_cents']) ? (int) $data['expected_amount_cents'] : null;

                if ($expectedCents !== null && $session->amount_total !== $expectedCents) {
                    Log::error('Stripe webhook amount mismatch', [
                        'session_id'     => $session->id,
                        'expected_cents' => $expectedCents,
                        'actual_cents'   => $session->amount_total,
                    ]);
                    return response('Amount mismatch', 200);
                }

                if ($session->payment_status === 'paid') {
                    $this->processPayment($session);
                }

                return response('OK', 200);
            } catch (\Exception $e) {
                Log::error('Stripe webhook error', ['error' => $e->getMessage()]);
                return response('Webhook handler error', 200);
            }
        }

        return response('OK', 200);
    }
}
