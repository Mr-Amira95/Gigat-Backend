<?php

namespace App\Providers;

use App\Events\MessageReadEvent;
use App\Events\PusherNewMessage;
use App\Listeners\TrackMetaLoginEvent;
use App\Listeners\UpdateMessageReadStatus;
use Illuminate\Auth\Events\Login;
use App\Models\Category;
use App\Models\User;
use Laravel\Passport\Passport;
use App\Observers\CategoryObserver;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Repositories\Eloquents\FaqRepository;
use App\Repositories\Eloquents\TagRepository;
use App\Repositories\Eloquents\AuthRepository;
use App\Repositories\Eloquents\PlanRepository;
use App\Repositories\Eloquents\RoleRepository;
use App\Repositories\Eloquents\AdminRepository;
use App\Repositories\Eloquents\ClientRepository;
use App\Repositories\Eloquents\SliderRepository;
use App\Repositories\Eloquents\TicketRepository;
use App\Repositories\Eloquents\CountryRepository;
use App\Repositories\Eloquents\RequestRepository;
use App\Repositories\Eloquents\ServiceRepository;
use App\Repositories\Eloquents\CategoryRepository;
use App\Repositories\Eloquents\ChatRepository;
use App\Repositories\Eloquents\FinanceRepository;
use App\Repositories\Eloquents\LanguageRepository;
use App\Repositories\Eloquents\WishlistRepository;
use App\Repositories\Eloquents\PortfolioRepository;
use App\Repositories\Eloquents\QuotationRepository;
use App\Repositories\Eloquents\FreelancerRepository;
use App\Repositories\Eloquents\ProfessionRepository;
use App\Repositories\Eloquents\SubCategoryRepository;
use App\Repositories\Eloquents\NotificationRepository;
use App\Repositories\Interfaces\FaqRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Interfaces\PlanRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use App\Repositories\Interfaces\SliderRepositoryInterface;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Repositories\Interfaces\CountryRepositoryInterface;
use App\Repositories\Interfaces\RequestRepositoryInterface;
use App\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\FinanceRepositoryInterface;
use App\Repositories\Interfaces\LanguageRepositoryInterface;
use App\Repositories\Interfaces\WishlistRepositoryInterface;
use App\Repositories\Interfaces\PortfolioRepositoryInterface;
use App\Repositories\Interfaces\QuotationRepositoryInterface;
use App\Repositories\Interfaces\FreelancerRepositoryInterface;
use App\Repositories\Interfaces\ProfessionRepositoryInterface;
use App\Repositories\Interfaces\SubCategoryRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use Illuminate\Support\Facades\View;
use App\Models\Currency;
use App\Models\General;
use App\Repositories\Eloquents\CompanyRepository;
use App\Repositories\Eloquents\DocumentCategoryRepository;
use App\Repositories\Eloquents\DocumentContentRepository;
use App\Repositories\Eloquents\FreelancerBankRepository;
use App\Repositories\Eloquents\PendingQuestionRepository;
use App\Repositories\Eloquents\ReleaseRepository;
use App\Repositories\Eloquents\ReportedIssueRepository;
use App\Repositories\Eloquents\RequestDeliveryRepository;
use App\Repositories\Eloquents\RequestFeedbackRepository;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use App\Repositories\Interfaces\DocumentCategoryRepositoryInterface;
use App\Repositories\Interfaces\DocumentContentRepositoryInterface;
use App\Repositories\Interfaces\FreelancerBankRepositoryInterface;
use App\Repositories\Interfaces\PendingQuestionRepositoryInterface;
use App\Repositories\Interfaces\ReleaseRepositoryInterface;
use App\Repositories\Interfaces\ReportedIssueRepositoryInterface;
use App\Repositories\Interfaces\RequestDeliveryRepositoryInterface;
use App\Repositories\Interfaces\RequestFeedbackRepositoryInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(FreelancerRepositoryInterface::class, FreelancerRepository::class);
        $this->app->bind(ProfessionRepositoryInterface::class, ProfessionRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(SliderRepositoryInterface::class, SliderRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SubCategoryRepositoryInterface::class, SubCategoryRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->bind(FaqRepositoryInterface::class, FaqRepository::class);
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->app->bind(WishlistRepositoryInterface::class, WishlistRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
        $this->app->bind(PortfolioRepositoryInterface::class, PortfolioRepository::class);
        $this->app->bind(RequestRepositoryInterface::class, RequestRepository::class);
        $this->app->bind(TicketRepositoryInterface::class, TicketRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(QuotationRepositoryInterface::class, QuotationRepository::class);
        $this->app->bind(ChatRepositoryInterface::class, ChatRepository::class);
        $this->app->bind(FinanceRepositoryInterface::class, FinanceRepository::class);
        $this->app->bind(PendingQuestionRepositoryInterface::class, PendingQuestionRepository::class);
        $this->app->bind(ReportedIssueRepositoryInterface::class, ReportedIssueRepository::class);
        $this->app->bind(FreelancerBankRepositoryInterface::class, FreelancerBankRepository::class);
        $this->app->bind(ReleaseRepositoryInterface::class, ReleaseRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(RequestDeliveryRepositoryInterface::class, RequestDeliveryRepository::class);
        $this->app->bind(RequestFeedbackRepositoryInterface::class, RequestFeedbackRepository::class);
        $this->app->bind(DocumentCategoryRepositoryInterface::class, DocumentCategoryRepository::class);
        $this->app->bind(DocumentContentRepositoryInterface::class, DocumentContentRepository::class);
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        Passport::tokensExpireIn(now()->addDays(1));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // Model::preventLazyLoading(!app()->isProduction());

        Model::preventLazyLoading(false);

        Category::observe(CategoryObserver::class);
        User::observe(UserObserver::class);
        Passport::enablePasswordGrant();

        Event::listen(Login::class, TrackMetaLoginEvent::class);
        // Event::listen(
        //     PusherNewMessage::class,
        //     // UpdateMessageReadStatus::class,
        // );


        View::composer('*', function ($view) {
            // Retrieve the whatsapp number value where key='whatsapp'
            $whatsappNumber = General::where('key', 'whatsapp')->value('value');

            $currencies = Currency::all()->map(function ($item) {
                return [
                    'code' => $item->code,
                    'exchange_rate' => $item->exchange_rate,
                    'symbol' => app()->getLocale() == 'ar' ? $item->symbol_ar : $item->symbol_en,
                ];
            });
            $logo = General::where('key', 'platform_logo')->value('value');

            $notificationCount = 0;
            if (Auth::check() && Auth::user() instanceof \App\Models\User) {

                $notificationCount = Auth::user()->notification()->where('is_read', 0)->count();
            }

            $currentCurrency = Session::get('currency', 'USD');
            $locale = App::getLocale(); // 'en' or 'ar'

            $currency = Currency::where('code', $currentCurrency)->first();

            $currencySymbol = $currency
                ? ($locale === 'ar' ? $currency->symbol_ar : $currency->symbol_en)
                : '$';


            $view->with('allcurrencies', $currencies)
                ->with('whatsappNumber', $whatsappNumber)
                ->with('notificationCount', $notificationCount)
                ->with('currencySymbol', $currencySymbol)
                ->with('currentCurrency', $currentCurrency)
                ->with('logo', $logo);
        });


        // 🔥 Set Locale From Session
        if (session()->has('locale')) {
            App::setLocale(session('locale'));
        }
    }

    protected function configureRateLimiting(): void
    {
        // Admin login: 5 attempts per minute per IP
        RateLimiter::for('admin-login', function ($request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () {
                return back()->withErrors(['login' => __('Too many login attempts. Please try again in a minute.')]);
            });
        });

        // Freelancer login: 5 attempts per minute per IP
        RateLimiter::for('freelancer-login', function ($request) {
            return Limit::perMinute(5)->by($request->ip())->response(function () {
                return back()->withErrors(['phone' => __('Too many login attempts. Please try again in a minute.')]);
            });
        });

        // Registration: 3 attempts per minute per IP (prevents mass account creation)
        RateLimiter::for('register', function ($request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // OTP verify / resend: 3 attempts per 10 minutes per IP
        RateLimiter::for('otp-verify', function ($request) {
            return Limit::perMinutes(10, 3)->by($request->ip());
        });

        // Password reset: 3 attempts per 5 minutes per IP
        RateLimiter::for('password-reset', function ($request) {
            return Limit::perMinutes(5, 3)->by($request->ip());
        });

        // Verify code: 5 attempts per 5 minutes per IP
        RateLimiter::for('verify-code', function ($request) {
            return Limit::perMinutes(5, 5)->by($request->ip());
        });
    }
}
