<?php

use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\FilterController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\GeneralController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\FreelancerController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PendingQuestionController;
use App\Http\Controllers\Admin\ProfessionController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\ReleaseController;
use App\Http\Controllers\Admin\ReportedIssueController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Api\ChatController as ApiChatController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\File\RequestFileController;
use App\Http\Controllers\Freelancer\AuthController as FreelancerAuthController;
use App\Http\Controllers\Freelancer\BotController;
use App\Http\Controllers\Freelancer\CallController;
use App\Http\Controllers\Freelancer\ChatController;
use App\Http\Controllers\Freelancer\FaqController as FreelancerFaqController;
use App\Http\Controllers\Freelancer\FinanceController as FreelancerFinanceController;
use App\Http\Controllers\Freelancer\HomeController as FreelancerHomeController;
use App\Http\Controllers\Freelancer\NotificationController as FreelancerNotificationController;
use App\Http\Controllers\Freelancer\PortfolioController as FreelancerPortfolioController;
use App\Http\Controllers\Freelancer\ProfileController;
use App\Http\Controllers\Freelancer\QuotationController as FreelancerQuotationController;
use App\Http\Controllers\Freelancer\RequestController as FreelancerRequestController;
use App\Http\Controllers\Freelancer\ReviewController as FreelancerReviewController;
use App\Http\Controllers\Freelancer\ServiceController as FreelancerServiceController;
use App\Http\Controllers\Freelancer\SocialLoginController;
use App\Http\Controllers\Freelancer\TicketController as FreelancerTicketController;
use App\Models\Currency;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Storage;

Route::get('/timezone-test', function () {
    return now()->toDateTimeString();
});



Route::get('language/{locale}', [HomeController::class, 'changeLocale'])
    ->name('locale.change')
    ->whereIn('locale', config('app.supported_locales', ['en', 'ar']));


Route::get('currency/{currency}', [FreelancerHomeController::class, 'changeCurrency'])
    ->name('currency.change');

Route::get('auth/google', [SocialLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);


// Guest Routes
Route::middleware('guest:admin')->group(function ($route) {
    $route->get('', [AuthController::class, 'showLoginForm'])->name('login');
    $route->post('login', [AuthController::class, 'login'])->name('login.submit');

    // Forgot Password Flow
    Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('forgot.form');
    Route::post('/forgot-password', [AuthController::class, 'submitForgot'])->name('forgot.submit');

    Route::get('/verify-code', [AuthController::class, 'showVerifyForm'])->name('verify.code.form');
    Route::post('/verify-code', [AuthController::class, 'submitVerify'])->name('verify.code.submit');

    Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('reset.form');
    Route::post('/reset-password', [AuthController::class, 'submitReset'])->name('reset.submit');
});
// Protected Routes
Route::middleware(['auth:admin', 'admin'])->group(function ($route) {
    $route->post('logout', [AuthController::class, 'logout'])->name('logout');
    $route->get('home', [HomeController::class, 'index'])->name('home.index');
    $route->controller(HomeController::class)->name('home.')->group(function ($route) {
        $route->get('home', 'index')->name('index');
    });


    $route->controller(NotificationController::class)->name('notifications.')->prefix('notifications')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('send', 'send')->name('send');
    });


    $route->controller(AdminController::class)->name('admins.')->prefix('admins')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('update-activation', 'updateActivation')->name('updateActivation');
    });
    $route->controller(RoleController::class)->name('roles.')->prefix('roles')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
    });
    $route->controller(ClientController::class)->name('clients.')->prefix('clients')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->post('update-activation', 'updateActivation')->name('updateActivation');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('update-activation', 'updateActivation')->name('updateActivation');
        $route->get('show/{id}', 'show')->name('show');
        $route->get('archived', 'archived')->name('archived');
        $route->post('/{id}/restore', 'restore')->name('restore');
    });
    $route->controller(FreelancerController::class)->name('freelancers.')->prefix('freelancers')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->post('update-activation', 'updateActivation')->name('updateActivation');
        $route->get('show/{id}', 'show')->name('show');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('update-activation', 'updateActivation')->name('updateActivation');
        $route->post('update-verification', 'updateVerification')->name('updateVerification');
        $route->get('archived', 'archived')->name('archived');
        $route->post('/{id}/restore', 'restore')->name('restore');
    });
    $route->controller(CompanyController::class)->name('companies.')->prefix('companies')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->get('show/{id}', 'show')->name('show');
    });
    $route->controller(ProfessionController::class)->name('professions.')->prefix('professions')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('update-activation', 'updateActivation')->name('updateActivation');
    });
    $route->controller(CountryController::class)->name('countries.')->prefix('countries')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('update-activation', 'updateActivation')->name('updateActivation');
    });
    $route->controller(SliderController::class)->name('sliders.')->prefix('sliders')->group(function ($route) {
        // Web sliders
        $route->get('web-sliders', 'webSliders')->name('webSliders');
        $route->get('create-web-sliders', 'createWebSliders')->name('createWebSliders');
        $route->post('store-web-slider', 'storeWebSlider')->name('storeWebSlider');
        $route->get('edit-web-sliders/{id}', 'editWebSliders')->name('editWebSliders');
        $route->put('update-web-slider/{id}', 'updateWebSlider')->name('updateWebSlider');

        // Mobile sliders
        $route->get('mobile-sliders', 'mobileSliders')->name('mobileSliders');
        $route->get('create-mobile-sliders', 'createMobileSliders')->name('createMobileSliders');
        $route->post('store-mobile-slider', 'storeMobileSlider')->name('storeMobileSlider');
        $route->get('edit-mobile-sliders/{id}', 'editMobileSliders')->name('editMobileSliders');
        $route->put('update-mobile-slider/{id}', 'updateMobileSlider')->name('updateMobileSlider');

        $route->get('move-up/{slider}', 'moveUp')->name('moveUp');
        $route->get('move-down/{slider}', 'moveDown')->name('moveDown');

        // Common routes
        $route->get('show/{id}', 'show')->name('show');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('update-activation', 'updateActivation')->name('updateActivation');
    });
    $route->controller(CategoryController::class)->name('categories.')->prefix('categories')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->get('show/{id}', 'show')->name('show');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('update-activation', 'updateActivation')->name('updateActivation');
        $route->post('update-popular-status', 'updatePopularStatus')->name('updatePopularStatus');
    });
    $route->controller(SubCategoryController::class)->name('subCategories.')->prefix('sub-categories')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('sub-categories-by-category-ids', 'subCategoriesByCategoryIds');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('update-activation', 'updateActivation')->name('updateActivation');
    });
    $route->controller(TagController::class)->name('tags.')->prefix('tags')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
    });
    $route->controller(ServiceController::class)->name('services.')->prefix('services')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->get('show/{id}', 'show')->name('show');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('toggle-recommended', 'toggleRecommended')->name('toggleRecommended');
        $route->post('toggle-activation', 'toggleActivation')->name('toggleActivation');
        $route->get('{freelancer}/categories', 'getCategoriesByFreelancer')->name('getCategoriesByFreelancer');
    });
    $route->controller(PortfolioController::class)->name('portfolios.')->prefix('portfolios')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->get('show/{id}', 'show')->name('show');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
    });
    $route->controller(RequestController::class)->name('requests.')->prefix('requests')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->get('show/{id}', 'show')->name('show');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
    });
    $route->controller(QuotationController::class)->name('quotations.')->prefix('quotations')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->get('show/{id}', 'show')->name('show');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
    });
    $route->controller(FinanceController::class)->name('finances.')->prefix('finances')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->post('bulk-update', 'bulkUpdate')->name('bulkUpdate');
        $route->get('export', 'export')->name('export');
    });
    $route->controller(ReviewController::class)->name('reviews.')->prefix('reviews')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
    });
    $route->controller(TicketController::class)->name('tickets.')->prefix('tickets')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('show/{id}', 'show')->name('show');
        $route->post('reply/{id}', 'reply')->name('reply');
        $route->put('{ticket}/change-status', 'changeStatus')->name('changeStatus');
    });
    $route->controller(ReportedIssueController::class)->name('reported-issues.')->prefix('reported-issues')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('show/{id}', 'show')->name('show');
        $route->put('{issue}/update-status/{status}', 'updateStatus')->name('updateStatus');
    });
    $route->controller(FilterController::class)->name('filters.')->prefix('filters')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
    });
    $route->controller(PlanController::class)->name('plans.')->prefix('plans')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->get('features/{id}', 'getFeaturesByPlan')->name('features');
    });
    $route->controller(FeatureController::class)->name('features.')->prefix('features')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->get('features/{id}', 'getFeaturesByPlan')->name('features');
    });
    $route->controller(FaqController::class)->name('faqs.')->prefix('faqs')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->get('show/{id}', 'show')->name('show');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('update-activation', 'updateActivation')->name('updateActivation');
    });
    $route->controller(PendingQuestionController::class)->name('pending-questions.')->prefix('pending-questions')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('show/{id}', 'show')->name('show');
        $route->post('convert/{id}', 'convertToFaq')->name('convert');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
    });

    $route->controller(GeneralController::class)->name('general.')->prefix('general')->group(function ($route) {
        $route->get('', 'generalInfo')->name('generalInfo');
        $route->put('update-general-info', 'updateGeneralInfo')->name('updateGeneralInfo');
        $route->get('privacy-policy', 'privacyPolicy')->name('privacyPolicy');
        $route->put('update-privacy-policy', 'updatePrivacyPolicy')->name('updatePrivacyPolicy');
        $route->get('terms', 'terms')->name('terms');
        $route->put('update-terms', 'updateTerms')->name('updateTerms');
    });

    $route->controller(AdminProfileController::class)->name('profile.')->prefix('profile')->group(function ($route) {
        $route->get('edit', 'edit')->name('edit');
        $route->put('update', 'update')->name('update');
    });

    Route::resource('releases', ReleaseController::class)->names('admin.releases');
    Route::post('releases/{id}/activation', [ReleaseController::class, 'updateActivation'])
        ->name('admin.releases.updateActivation');

    Route::resource('currencies', CurrencyController::class);
    Route::post('categories/update-popular-status', [CategoryController::class, 'updatePopularStatus'])->name('categories.updatePopularStatus');

    // Route::resource('chats', AdminChatController::class);
    Route::prefix('chats')->name('chats.')->group(function () {
        Route::get('/', [AdminChatController::class, 'index'])->name('index');
        Route::get('/show', [AdminChatController::class, 'show'])->name('show');
        Route::get('/freelancers-by-client', [AdminChatController::class, 'getFreelancersByClient'])->name('freelancersByClient');
    });

});






Route::prefix('freelancer')->middleware('guest:freelancer')->group(function ($route) {
    // login
    $route->get('login', [FreelancerAuthController::class, 'showLoginForm'])->name('freelancer.login');
    $route->post('login', [FreelancerAuthController::class, 'login'])->name('freelancer.login.submit');
    $route->post('websiteLogin', [FreelancerAuthController::class, 'websiteLogin'])->name('freelancer.weblogin');

    // register
    $route->get('register', [FreelancerAuthController::class, 'showRegisterForm'])->name('freelancer.register');
    $route->post('register', [FreelancerAuthController::class, 'register'])->name('freelancer.register.submit');

    // phone verification
    $route->get('verify-phone', [FreelancerAuthController::class, 'showVerifyPhoneForm'])->name('freelancer.verify.phone');
    $route->post('verify-phone', [FreelancerAuthController::class, 'verifyPhone'])->name('freelancer.verify.phone.submit');
    $route->post('resend-phone-code', [FreelancerAuthController::class, 'resendPhoneCode'])->name('freelancer.resend.phone.code');
});





Route::middleware(['auth:freelancer', 'freelancer'])->prefix('freelancer')->name('freelancer.')->group(function ($route) {
    Route::post('save-player-id', [SocialLoginController::class, 'savePlayerId'])->name('savePlayerId');

    $route->post('logout', [FreelancerAuthController::class, 'logout'])->name('logout');
    $route->get('home', [FreelancerHomeController::class, 'index'])->name('home.index');
    $route->controller(FreelancerHomeController::class)->name('home.')->group(function ($route) {
        $route->get('home', 'index')->name('index');
    });

    $route->controller(ChatController::class)->name('chat.')->prefix('chat')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('show-chat/{chatId}', 'showChat')->name('show');
        $route->post('send-message/{chatId}', 'sendMessage')->name('sendMessage')->middleware('check.blocked');
    });
    $route->controller(CallController::class)->name('call.')->prefix('call')->group(function ($route) {
        $route->get('start-call/{receiverId}', 'startCall')->name('start');
        $route->get('answer-call/{callId}', 'answerCall')->name('answer');
        $route->get('end-call/{callId}', 'endCall')->name('end');
        $route->get('status/{callId}', 'status')->name('status');
    });
    $route->controller(FreelancerNotificationController::class)->name('notification.')->prefix('notification')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->post('read/{id}', 'markAsRead')->name('read');
    });


    $route->controller(ProfessionController::class)->name('professions.')->prefix('professions')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('update-activation', 'updateActivation')->name('updateActivation');
    });


    $route->controller(FreelancerServiceController::class)->name('services.')->prefix('services')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit')->middleware('owns:service');
        $route->get('show/{id}', 'show')->name('show')->middleware('owns:service');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('toggle-recommended', 'toggleRecommended')->name('toggleRecommended');
    });

    $route->controller(ProfileController::class)->name('profile.')->prefix('profile')->group(function ($route) {
        $route->get('show', 'show')->name('show');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->post('change-password', 'updatePassword')->name('updatePassword');
        $route->post('verify', 'verify')->name('verify');
        $route->delete('certificate/{id}', 'deleteCertificate')->name('deleteCertificate');
    });


    $route->controller(FreelancerPortfolioController::class)->name('portfolios.')->prefix('portfolios')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit')->middleware('owns:portfolio');
        $route->put('update/{id}', 'update')->name('update');
        $route->get('show/{id}', 'show')->name('show')->middleware('owns:portfolio');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
    });
    $route->controller(FreelancerRequestController::class)->name('requests.')->prefix('requests')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit')->middleware('owns:request');
        $route->put('update/{id}', 'update')->name('update');
        $route->get('show/{id}', 'show')->name('show')->middleware('owns:request');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->post('change-status/{id}', 'changeStatus')->name('changeStatus');
        $route->get('{id}/logs', 'logs')->name('logs');
        $route->post('{id}/add-update', 'addUpdate')->name('addUpdate');
        $route->get('{id}/download-contract', 'downloadContract')->name('downloadContract');
    });
    $route->controller(FreelancerQuotationController::class)->name('quotations.')->prefix('quotations')->group(function ($route) {
        $route->get('', 'index')->name('index');
        // $route->get('create', 'create')->name('create');
        // $route->post('store', 'store')->name('store');
        // $route->get('edit/{id}', 'edit')->name('edit');
        // $route->put('update/{id}', 'update')->name('update');
        $route->get('show/{id}', 'show')->name('show');
        // $route->delete('destroy/{id}', 'destroy')->name('destroy');
        $route->get('create-comment/{id}', 'createComment')->name('comment.create');
        $route->post('store-comment/{id}', 'storeComment')->name('comment.store');
    });
    $route->controller(FreelancerFinanceController::class)->name('finances.')->prefix('finances')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->post('bulk-update', 'bulkUpdate')->name('bulkUpdate');
    });
    $route->controller(BotController::class)->name('ai.')->prefix('ai')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->post('send', 'sendMessage')->name('send');
        $route->post('delete', 'deleteMessages')->name('delete');
    });
    $route->controller(FreelancerReviewController::class)->name('reviews.')->prefix('reviews')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
    });
    $route->controller(FreelancerTicketController::class)->name('tickets.')->prefix('tickets')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('show/{id}', 'show')->name('show')->middleware('owns:ticket');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->post('reply/{id}', 'reply')->name('reply');
        $route->put('{ticket}/change-status', 'changeStatus')->name('changeStatus');
    });
    $route->controller(FilterController::class)->name('filters.')->prefix('filters')->group(function ($route) {
        $route->get('', 'index')->name('index');
        $route->get('create', 'create')->name('create');
        $route->post('store', 'store')->name('store');
        $route->get('edit/{id}', 'edit')->name('edit');
        $route->put('update/{id}', 'update')->name('update');
        $route->delete('destroy/{id}', 'destroy')->name('destroy');
    });


    $route->controller(FreelancerFaqController::class)->name('faqs.')->prefix('faqs')->group(function ($route) {
        $route->get('', 'index')->name('index');
    });
});

Route::get('/service_details', function (Request $request) {
    $id = $request->query('id');   // ✅ correct way

    $agent = new Agent();

    if ($agent->isMobile() || $agent->isTablet()) {
        return redirect("https://dashboard.gigat.app/service_details?id=$id");
    }

    return redirect("https://gigat.app/service/$id");
});

Broadcast::routes(['middleware' => ['auth:freelancer']]);


Route::prefix('stripe')->controller(StripeController::class)->group(function () {
    Route::get('success', 'success')->name('stripe.success');
    Route::get('cancel', 'cancel')->name('stripe.cancel');
    Route::post('webhook', 'handleWebhook')->name('stripe.webhook');
});

// View All Files
Route::get('requests/{id}/delivery/files', [RequestFileController::class, 'deliveryFiles'])->name('requests.delivery.files')->middleware('signed');
Route::get('requests/{id}/feedback/files', [RequestFileController::class, 'feedbackFiles'])->name('requests.feedbacks.files')->middleware('signed');

// Download All Files
Route::get('requests/{id}/delivery/download-all', [RequestFileController::class, 'downloadAllDelivery'])->name('download.all.delivery');
Route::get('requests/{id}/feedback/download-all', [RequestFileController::class, 'downloadAllFeedback'])->name('download.all.feedback');
