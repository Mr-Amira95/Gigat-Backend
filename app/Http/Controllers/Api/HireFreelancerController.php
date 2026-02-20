<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HireFreelancerRequest;
use App\Services\ServiceService;
use App\Services\RequestService;
use App\Utilities\CurrencyConverter;
use App\Mail\NewRequestClientMail;
use App\Mail\NewRequestFreelancerMail;
use App\Models\User;
use App\Services\ContractGeneratorService;
use App\Services\NoticeService;
use Illuminate\Support\Facades\Mail;

class HireFreelancerController extends Controller
{

    private $serviceService;
    private $requestService;
    private $contractGenerator;
    private $noticeService;

    public function __construct(ServiceService $serviceService, RequestService $requestService, ContractGeneratorService $contractGenerator, NoticeService $noticeService) {
        $this->serviceService = $serviceService;
        $this->requestService = $requestService;
        $this->contractGenerator = $contractGenerator;
        $this->noticeService = $noticeService;
    }

        public function store(HireFreelancerRequest $request)
    {
        try {

            $client = User::auth()->user();
            $freelancer = User::where('id', $request->freelancer_id)->firstOrFail();

            /** 1. Convert price to USD */
            $priceUsd = CurrencyConverter::convert(
                $request->price,
                $request->currency,
                'USD'
            );

            /** 2. Create TEMP service for freelancer */
            $serviceData = [
                'user_id'        => $freelancer->id,
                'delivery_days'  => $request->delivery_days,
                'plans' => [
                    [
                        'features' => [
                            ['type' => 'title',  'value' => $request->service_title],
                            ['type' => 'price',  'value' => $priceUsd],
                            ['type' => 'revision', 'value' => $request->revisions],
                            ['type' => 'source_files', 'value' => $request->source_files],
                        ]
                    ]
                ],
                'translations' => [
                    'en' => [
                        'title'       => $request->service_title,
                        'description' => $request->service_description,
                    ]
                ]
            ];

            $service = $this->serviceService->create($serviceData);

            /** 3. Upload attachments */
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $attachments[] = $file->store('requests', 'public');
                }
            }

            /** 4. Create request */
            $requestPayload = [
                'user_id'        => $client->id,
                'freelancer_id'  => $freelancer->id,
                'service_id'     => $service->id,
                'revision'       => $request->revisions,
                'source_files'   => $request->source_files,
                'attachments'    => $attachments,
                'status'         => 'pending',
            ];

            $response = $this->requestService->createRequest($requestPayload);

            // 5. Generate PDF contract
            $contractData = [
                '{[client_name]}'       => $client->username,
                '{[client_email]}'      => $client->email,
                '{[client_phone]}'      => $client->prefix . $client->phone,
                '{[freelancer_name]}'   => $freelancer->username,
                '{[freelancer_email]}'  => $freelancer->email,
                '{[freelancer_phone]}'  => $freelancer->prefix . $freelancer->phone,

                '{[contract]}'   => $response['data']['order_number'],
                '{[invoice]}'    => $response['data']['invoice_number'] ?? 'INV-' . $response['data']['id'],
                '{[date]}'              => now()->format('Y-m-d'),

                '{[service_title]}'     => $response['data']['service']->translations()->where('language', 'en')->first()->title,
                '{[delivery_date]}'     => $response['data']['delivery_date'],

                '{[service_price]}'     => '$' . $response['data']['finance']->amount,
                '{[commission]}'        => '$' . $response['data']['finance']->commission,
                '{[tax]}'               => '$' . $response['data']['finance']->fees,
                '{[total_amount]}'      => '$' . $response['data']['finance']->total,

                '{[revisions]}'         => $response['data']['revision'],

            ];

            $fileName = substr($response['data']['order_number'], 1);

            $pdfUrl = $this->contractGenerator->generate($contractData, $fileName);
            $response['request']->update(['contract_path' => $pdfUrl,]);

            /** 6. Soft delete service */
            $service->delete();

            // 7. Send notification to freelancer
            $titles = [
                'en' => __('messages.new_request_title', [], 'en'),
                'ar' => __('messages.new_request_title', [], 'ar'),
            ];

            $messages = [
                'en' => __('messages.new_request_message', [
                    'order_number' => $response['request']->order_number
                ], 'en'),
                'ar' => __('messages.new_request_message', [
                    'order_number' => $response['request']->order_number
                ], 'ar'),
            ];

            $this->noticeService->send(
                $request->freelancer_id,
                $titles,
                $messages,
                'request',
                $response['data']['id'],
                true
            );

            // 8. Send emails to client and freelancer
            Mail::to($response['request']->user->email)->queue(
                new NewRequestClientMail(
                    $response['request'],
                    $response['finance'],
                    $pdfUrl
                    // 'files/freelancer/f0c9395f3b1a5b6553d60be1d5fc792c.pdf'
                )
            );

            // mail to freelancer
            Mail::to($freelancer->email)->queue(
                new NewRequestFreelancerMail(
                    $response['request'],
                    $response['finance'],
                    $pdfUrl
                    // 'files/freelancer/f0c9395f3b1a5b6553d60be1d5fc792c.pdf'

                )
            );


            return $this->successResponse(__('success'), $response);

        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function payRequest($id)
    {
        try {
            $response = $this->requestService->payRequest($id);

            return $this->successResponse(__('success'), $response);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}