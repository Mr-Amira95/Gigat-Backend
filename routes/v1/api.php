<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    BlockController,
    BotController,
    CallController,
    CertificateController,
    HomeController,
    UserController,
    FilterController,
    NotificationController,
    SharedController,
    ServiceController,
    WishlistController,
    CategoryController,
    ChatController,
    CheckoutController,
    CompanyController,
    CurrencyController,
    FaqController,
    FinanceController,
    FinancialController,
    FreelancerBankController,
    PortfolioController,
    QuotationController,
    SubCategoryController,
    RequestController,
    SocialAuthController,
    TagController,
    TicketController,
    PendingQuestionController,
    ReleaseController,
    ReportedIssueController,
    RequestDeliveryController,
    RequestFeedbackController,
    StripeController,
    ChatbotController,
    HireFreelancerController,
    RatingController,
    AnalyticsController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

Route::get('/test-time', function () {
    return [
        'now' => now()->toDateTimeString(),
        'timezone' => config('app.timezone'),
    ];
});

// Auth Routes
Route::controller(AuthController::class)->group(function ($route) {
    $route->post('login', 'login')->middleware('throttle:10,1');
    $route->post('social-login', 'socialLogin')->middleware('throttle:10,1');
    $route->post('register', 'register')->middleware('throttle:5,1');
    $route->post('generate-code', 'generateCode')->middleware('throttle:5,5');
    $route->post('verify-code', 'verifyCode')->middleware('throttle:10,5');
    $route->post('reset-password', 'resetPassword')->middleware('throttle:5,5');

    $route->post('create-freelancer', 'createFreelancer')->middleware('throttle:5,1');
    $route->post('web-login', 'webLogin')->middleware('throttle:10,1');
});
Route::controller(SocialAuthController::class)->prefix('auth')->group(function ($route) {
    $route->get('{provider}', [SocialAuthController::class, 'redirectToProvider']);
    $route->get('{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
});

// Public Routes
Route::controller(HomeController::class)->group(function ($route) {
    $route->get('sliders', 'sliders');
    $route->get('home', 'homeMobile');
    $route->get('home-page', 'homePage');
});
Route::controller(SubCategoryController::class)->group(function ($route) {
    $route->get('sub-categories', 'getByCategoryId');
});
Route::controller(TagController::class)->prefix('tags')->group(function ($route) {
    $route->get('', 'getAllTags');
    $route->get('by_sub_category/{id}', 'getTagBySubCategory');
});
Route::prefix('filters')->controller(FilterController::class)->group(function ($route) {
    // $route->get('', 'getFiltersByCategoryId');
    $route->get('', 'getFilterValues');
    $route->post('apply', 'applyFilters');
});
Route::prefix('portfolio')->controller(PortfolioController::class)->group(function ($route) {
    $route->get('', 'getPortfolioByUserId');
    $route->get('featured', 'getFeaturedPortfolios');
    $route->get('details/{id}', 'portfolioDetails');
});
Route::prefix('shared')->controller(SharedController::class)->group(function ($route) {
    $route->get('countries', 'getCountries');
    $route->get('languages', 'getLanguages');
    $route->get('general-data', 'generalData');
    $route->get('register-data', 'getRegisterData');
    $route->get('plans', 'getPlans');
});
Route::prefix('categories')->controller(CategoryController::class)->group(function ($route) {
    $route->get('', 'index');
    $route->get('details/{id}', 'details');
});
Route::prefix('services')->controller(ServiceController::class)->group(function ($route) {
    $route->get('', 'getBySubCategory');
    $route->get('recommended', 'getRecommendedServices');
    $route->get('get-by-user', 'getServicesByUserId');
    $route->get('featured', 'getFeaturedServices');
    $route->get('details/{service_id}', 'serviceDetails');
    $route->get('search/{query?}', 'search');
    $route->get('{slug}', 'getServicesByTag');
    $route->get('search-services-and-sub-categories/search', 'searchServicesAndSubCategories');
});

Route::prefix('notifications')->controller(NotificationController::class)->group(function ($route) {
    $route->get('', 'getNotifications');
    $route->put('mark-as-read/{id}', 'markAsReadByNotification');
    $route->post('change-notifiable', 'changeNotifiable');
});
Route::prefix('currencies')->controller(CurrencyController::class)->group(function ($route) {
    $route->get('', 'index');
});
Route::prefix('faqs')->controller(FaqController::class)->group(function ($route) {
    $route->get('', 'index');
});
Route::prefix('pending-questions')->controller(PendingQuestionController::class)->group(function ($route) {
    $route->post('', 'store');
});
Route::prefix('users')->controller(UserController::class)->group(function ($route) {
    $route->get('freelancer-profile/{userId}', 'getFreelancerProfileByUserId')->middleware('check.blocked');
    $route->get('freelancer-categories', 'getFreelancerCategoriesByUserId');
});
Route::prefix('report-issue')->controller(ReportedIssueController::class)->group(function ($route) {
    $route->post('', 'store');
});
Route::apiResource('releases', ReleaseController::class);

Route::get('company-details/{id}', [CompanyController::class, 'getCompanyById']);
Route::post('company-register', [CompanyController::class, 'registerCompany']);


// Stripe webhook must be OUTSIDE auth:api — Stripe does not send an Authorization header.
// Security is enforced inside handleWebhook() via Webhook::constructEvent() signature verification.
Route::post('stripe/webhook', [StripeController::class, 'handleWebhook']);

// Protected Routes (Require Authentication)
Route::middleware('auth:api')->group(function () {

    Route::prefix('auth')->controller(AuthController::class)->group(function ($route) {
        $route->post('logout', 'logout');
        $route->get('freelancer/redirect', 'generateFreelancerRedirect');
        $route->delete('account', 'deleteAccount');
    });

    Route::post('/chatbot', [ChatbotController::class, 'chatbot']);

    Route::controller(HomeController::class)->group(function ($route) {
        $route->get('home-freelancer', 'homeFreelancer');
    });

    Route::prefix('users')->controller(UserController::class)->group(function ($route) {
        $route->post('complete-profile', 'completeProfile');
        $route->post('verify', 'uploadFileVerification');
        $route->get('client-profile', 'getClientProfile');
        $route->get('freelancer-profile', 'getFreelancerProfile');
        $route->post('update-profile', 'updateProfile');
        $route->post('change-password', 'changePassword');
    });

    Route::prefix('company')->controller(CompanyController::class)->group(function ($route) {
        $route->post('', 'store')->middleware('freelancer.api');
        $route->post('update', 'update')->middleware('freelancer.api');
        $route->get('details', 'getFreelancerCompany')->middleware('freelancer.api');
    });


    Route::prefix('wishlist')->controller(WishlistController::class)->group(function ($route) {
        $route->post('toggle', 'toggle');
        $route->get('', 'getWishlistByUserId');
    });

    Route::prefix('notifications')->controller(NotificationController::class)->group(function ($route) {
        $route->post('read', 'markAsRead');
    });

    // Route::prefix('requests')->controller(RequestController::class)->group(function ($route) {
    //     $route->get('', 'getByUser');
    //     $route->post('create', 'createRequest');
    //     $route->get('details/{id}', 'requestDetails');
    //     $route->post('add-comment', 'addComment');
    //     $route->get('by-freelancer', 'getByFreelancer');
    //     $route->post('confirm-request/{id}', 'confirmRequest');
    // });

    Route::prefix('requests')->group(function ($route) {

        // RequestController
        $route->controller(RequestController::class)->group(function ($route) {
            $route->get('', 'getByUser');
            $route->post('create', 'createRequest');
            $route->get('details/{id}', 'requestDetails');
            $route->post('add-comment', 'addComment');
            $route->get('by-freelancer', 'getByFreelancer');
            $route->post('confirm-request/{id}', 'confirmRequest');
        });

        // -------------------------
        // Delivery: Freelancer only
        // -------------------------
        $route->controller(RequestDeliveryController::class)->group(function ($route) {
            $route->post('{id}/deliver', 'store')->middleware(['freelancer.api', 'owns:request']);
            $route->post('{id}/deliver/{delivery_id}', 'update')->middleware(['freelancer.api', 'owns:request']);
            $route->delete('deliver/attachments/{id}', 'deleteAttachment')->middleware(['freelancer.api']);
            $route->get('{id}/deliveries', 'getRequestDeliveries');
        });

        // ------------------------------------------------------
        // Feedback: Client only (approve/reject request)
        // ------------------------------------------------------
        $route->controller(RequestFeedbackController::class)->group(function ($route) {
            $route->post('{id}/feedback', 'store');
        });
    });

    Route::prefix('finances')->controller(FinanceController::class)->group(function ($route) {
        $route->get('client', 'getClientFinancialRecords');
        $route->get('freelancer', 'getFreelancerFinancialRecords');
    });
    Route::prefix('freelancer-bank')->controller(FreelancerBankController::class)->group(function ($route) {
        $route->get('', 'index');
        $route->post('', 'updateOrCreate');
    });
    Route::prefix('tickets')->controller(TicketController::class)->group(function ($route) {
        $route->get('', 'userTickets');
        $route->post('submit-ticket', 'store');
        $route->post('add-response', 'addMessage');
        $route->get('{id}', 'show')->middleware(['owns:ticket']);
        $route->post('close-ticket/{id}', 'closeTicket')->middleware('owns:ticket');
    });

    Route::prefix('quotations')->controller(QuotationController::class)->group(function ($route) {
        $route->get('', 'getAll');
        $route->get('get-by-user-id', 'getByUserId');
        $route->get('get-by-freelancer-id', 'getByFreelancerId');
        $route->post('create', 'createQuotation');
        $route->post('approve-quotation/{id}', 'approveQuotation');
        $route->get('/details/{id}', 'findById');
        $route->post('create-comment', 'createComment');
        $route->get('/comment-list/{quotationId}', 'getCommentsByQuotationId');
        $route->delete('{id}', 'destroy');
    });

    Route::prefix('hire-freelancer')->controller(HireFreelancerController::class)->group(function ($route) {
        $route->post('', 'store');
    });
    // Route::post('/hire-freelancer', action: [HireFreelancerController::class, 'store']);
    // Route::post('/pay-request/{id}', action: [HireFreelancerController::class, 'payRequest']);

    Route::prefix('services')->controller(ServiceController::class)->group(function ($route) {
        $route->post('create', 'create')->middleware('freelancer.api');
        $route->post('update/{id}', 'update')->middleware(['freelancer.api', 'owns:service']);
        $route->delete('delete/{id}', 'delete')->middleware(['freelancer.api', 'owns:service']);
        $route->delete('delete-media/{id}', 'deleteMedia')->middleware('freelancer.api');
        $route->patch('activation/{id}', 'toggleActivation')->middleware(['freelancer.api', 'owns:service']);
    });
    Route::prefix('rating')->controller(RatingController::class)->group(function ($route) {
        $route->post('rate-client', 'rateClient')->middleware('freelancer.api');
        $route->post('rate-freelancer', 'rateFreelancer');
    });
    Route::prefix('portfolio')->controller(PortfolioController::class)->group(function ($route) {
        $route->post('create', 'create')->middleware('freelancer.api');
        $route->post('update/{id}', 'update')->middleware(['freelancer.api', 'owns:portfolio']);
        $route->delete('delete/{id}', 'delete')->middleware(['freelancer.api', 'owns:portfolio']);
        $route->delete('delete-media/{id}', 'deleteMedia')->middleware('freelancer.api');
    });
    Route::prefix('certificates')->controller(CertificateController::class)->group(function ($route) {
        $route->delete('{id}', 'delete')->middleware(['freelancer.api', 'owns:certificate']);
    });
    Route::prefix('checkout')->controller(CheckoutController::class)->group(function ($route) {
        $route->post('proceed', 'proceedCheckout');
    });
    Route::prefix('chat')->controller(ChatController::class)->group(function ($route) {
        $route->post('start-chat', 'startChat');
        $route->post('send-message', 'sendMessage')->middleware('check.blocked');
        $route->get('get-messages/{chatId}', 'getMessages');
        $route->get('unread-count/{chatId}', 'unreadCount');
        $route->post('mark-read/{chatId}', 'markAsRead');
        $route->get('get-chat', 'getAllChats');
        $route->post('toggle-flag', 'toggleFlag');
        // $route->post('update-status/{id}', 'updateStatus');
        // $route->post('get-voice-call-token', 'getVoiceCallToken');
    });

    Route::prefix('chat')->controller(CallController::class)->group(function ($route) {
        // $route->post('update-status/{id}', 'updateStatus');
        $route->post('start-call', 'startCall');
        $route->post('answer-call', 'answerCall');
        $route->post('end-call', 'endCall');
    });

    Route::prefix('bot')->controller(BotController::class)->group(function ($route) {
        $route->get('get-messages', 'getMessages');
        $route->post('send-message', 'sendMessage');
        $route->delete('delete-messages', 'deleteMessages');
    });


    Route::prefix('stripe')->controller(StripeController::class)->group(function ($route) {
        $route->post('checkout', 'createCheckoutSession');
    });

    Route::prefix('block')->controller(BlockController::class)->group(function ($route) {
        $route->get('', 'list');
        $route->post('', 'blockOrUnblock');
    });
    // // stripe route
    // Route::post('stripe/create-checkout-session', [StripeController::class, 'createSession']);
    // Route::post('stripe/webhook', [StripeController::class, 'handleWebhook']);
});

// Analytics — public endpoints (auth is optional on /event to attach user_id)
Route::prefix('analytics')->controller(AnalyticsController::class)->group(function ($route) {
    $route->post('visitor', 'visitor')->middleware('throttle:60,1');
    $route->post('event', 'event')->middleware('throttle:120,1');
});

// Route::middleware('auth:api')->post('/broadcasting/auth', function (Request $request) {
//     return Broadcast::auth($request);
// });
Route::middleware('auth:api')->post('/broadcasting/auth', function (Request $request) {
    // Log::info('Broadcasting auth request:', $request->all());
    // Log::info('Authenticated User:', ['user' => auth()->user()]);
    $response = Broadcast::auth($request);
    // Log::info('Broadcasting auth response:', ['response' => $response]);

    return $response;
});
