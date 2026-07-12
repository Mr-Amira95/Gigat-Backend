<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateFreelancerRequest;
use App\Http\Requests\Api\GenerateCodeRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\VerifyCodeRequest;
use App\Http\Resources\UserResource;
use App\Models\Notification;
use App\Models\PlayerId;
use App\Models\User;
use App\Services\FreelancerService;
use App\Services\MetaConversionsApiService;
use App\Services\NoticeService;
use App\Services\OneSignalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $authService;
    protected $freelancerService;
    protected $noticeService;
    protected $metaService;


    public function __construct(AuthService $authService, FreelancerService $freelancerService, NoticeService $noticeService, MetaConversionsApiService $metaService)
    {
        $this->authService = $authService;
        $this->freelancerService = $freelancerService;
        $this->noticeService    = $noticeService;
        $this->metaService      = $metaService;
    }

    public function register(RegisterRequest $request)
    {
        try {
            $result = $this->authService->register($request->validated());
            $this->metaService->dispatchEvent($result, $request, 'Client Register');
            return $this->successResponse(__('success'), 201);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Login user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->validated());
            $user = $result['user'];

            if ($request->input('player_id')) {
                $exists = PlayerId::where('user_id', $user->id)
                    ->where('player_id', $request->player_id)
                    ->where('platform', $request->platform)
                    ->exists();

                if (!$exists) {
                    PlayerId::create([
                        'user_id'   => $user->id,
                        'player_id' => $request->player_id,
                        'platform'  => $request->platform,
                    ]);
                }
            }

            if (!$result['verified']) {
                $this->authService->generateCode($user);
                return $this->successResponse(__('account_is_not_verified'), [
                    'user'  => new UserResource($user),
                    'token' => null,
                ]);
            }

            $this->metaService->dispatchEvent($user, $request, $user->freelancer ? 'Freelancer Login' : 'Client Login');

            return $this->successResponse(__('login_successful'), [
                'user'  => new UserResource($user),
                'token' => $result['token'],
            ]);
        } catch (Exception $e) {
            return $this->exceptionResponse($e, $e->getMessage());
        }
    }

    public function webLogin(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->validated());
            $user = $result['user'];

            if ($request->input('player_id')) {
                $exists = PlayerId::where('user_id', $user->id)
                    ->where('player_id', $request->player_id)
                    ->where('platform', $request->platform)
                    ->exists();

                if (!$exists) {
                    PlayerId::create([
                        'user_id'   => $user->id,
                        'player_id' => $request->player_id,
                        'platform'  => $request->platform,
                    ]);
                }
            }

            if (!$result['verified']) {
                $this->authService->generateCode($user);
                return $this->successResponse(__('account_is_not_verified'), [
                    'user'  => new UserResource($user),
                    'token' => null,
                ]);
            }

            if ($user->freelancer) {
                DB::table('sessions')->where('user_id', $user->id)->delete();
                $encryptedId = encrypt($user->id);
                $url = route('freelancer.login', ['token' => $encryptedId]);

                return $this->successResponse(__('login_successful'), [
                    'redirect_to' => $url,
                    'user'        => new UserResource($user),
                    'token'       => $result['token'],
                ]);
            }

            $this->metaService->dispatchEvent($user, $request, 'Client Login');

            return $this->successResponse(__('login_successful'), [
                'user'  => new UserResource($user),
                'token' => $result['token'],
            ]);
        } catch (Exception $e) {
            return $this->exceptionResponse($e, $e->getMessage());
        }
    }

    public function generateFreelancerRedirect(Request $request)
    {
        try {
            $userId = Auth()->id();

            // Delete all sessions for this user
            DB::table('sessions')->where('user_id', $userId)->delete();

            $encryptedId = encrypt($userId);
            $url = route('freelancer.login', ['token' => $encryptedId]);

            return $this->successResponse(__('login_successful'), [
                'redirect_to' => $url,
                // 'user' => new UserResource($result['user']),
            ]);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function socialLogin(Request $request)
    {
        $request->validate([
            'google_id' => 'nullable|string|required_without:apple_id',
            'apple_id'  => 'nullable|string|required_without:google_id',
            'email'     => 'required|string|email',
            'player_id' => 'nullable|string',
            'platform'  => 'nullable|string',
        ]);

        try {
            $user = User::with(['languages.language'])
                ->where('email', $request->email)
                ->first();

            if (!$user) {
                return $this->successResponse(__('user_not_found'));
            }

            // Determine provider
            $provider = $request->google_id ? 'google_id' : 'apple_id';
            $providerId = $request->$provider;

            // Validate provider ID match
            if ($user->$provider && $user->$provider !== $providerId) {
                return $this->successResponse(__('user_not_found'));
            }

            // Reject auto-linking: if account exists but has no social ID, force password login first
            if (!$user->$provider) {
                return $this->errorResponse(
                    'Account exists. Please login with password then link ' . ($provider === 'google_id' ? 'Google' : 'Apple') . ' from your profile.',
                    401
                );
            }

            // Handle player_id
            if ($request->filled('player_id')) {
                PlayerId::firstOrCreate([
                    'user_id'   => $user->id,
                    'player_id' => $request->player_id,
                    'platform'  => $request->platform,
                ]);
            }

            $token = $user->createToken('User Token')->accessToken;

            $this->metaService->dispatchEvent($user, $request, $user->freelancer ? 'Freelancer Login' : 'Client Login');

            return $this->successResponse(__('login_successful'), [
                'user'  => new UserResource($user),
                'token' => $token
            ]);

        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function SendNotification($userId)
    {
        // one signal notification
        $user = User::where('id', $userId)->first();
        if ($user) {
            $playerIdRecord = PlayerId::where('user_id', $user->id)
                ->where('is_notifiable', 1)
                ->pluck('player_id')->toArray();


            if ($playerIdRecord) {
                $titles = [
                    'en' => __('notifications.item_added_title', [], 'en'),
                    'ar' => __('notifications.item_added_title', [], 'ar'),
                ];

                $messages = [
                    'en' => __('notifications.item_added_message', [], 'en'),
                    'ar' => __('notifications.item_added_message', [], 'ar'),
                ];

                $response = app(OneSignalService::class)->sendNotificationToUserCall(
                    $playerIdRecord, // نرسل player_id من جدول player_ids
                    $titles,
                    $messages,
                    'call',
                    1
                );

                Notification::create([
                    'user_id'           => $user->id,
                    'title'             => json_encode($titles),
                    'body'              => json_encode($messages),
                    'type'              => 'call',
                    'type_id'           => 1,
                    'is_read'           => false,
                    'onesignal_id'      => $response['id'] ?? null,
                    'response_onesignal' => json_encode($response),
                ]);
            }
        }
        // *********************************************//

        return $this->successResponse('sent');
    }


    public function generateCode(GenerateCodeRequest $request)
    {
        try {
            $result = $this->authService->findByPhoneAndPrefix($request->validated());
            if (!$result['user']) {
                return $this->errorResponse(__('user_not_found'), 404);
            }

            $user = $result['user'];
            $this->authService->generateCode($user);
            return $this->successResponse(__('code_generated_successfully'), [
                'email' => $user->email,
                'phone' => $user->phone,
            ]);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Verify code
     *
     * @param VerifyCodeRequest $request
     * @return JsonResponse
     */


    public function verifyCode(VerifyCodeRequest $request)
    {
        try {
            $result = $this->authService->findByPhoneAndPrefix($request->validated());
            if (!$result['user']) {
                return $this->errorResponse(__('user_not_found'), 404);
            }

            if ($result['user']->code_expires_at && $result['user']->code_expires_at->isPast()) {
                return $this->errorResponse(__('verification_code_expired'), 422);
            }

            if ($request['code'] !== $result['user']->code) {
                return $this->errorResponse(__('invalid_verification_code'));
            }
            $result = $this->authService->verifyCode($result['user']);

            // Store OTP-verified token so resetPassword can confirm OTP was completed
            Cache::put('pwd_reset_user_' . $result['user']->id, true, now()->addMinutes(10));

            return $this->successResponse(__('code_verified_successfully'), [
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ]);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Reset password
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */


    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $result = $this->authService->findByPhoneAndPrefix($request->validated());
            if (!$result['user']) {
                return $this->errorResponse(__('user_not_found'), 404);
            }

            // Ensure OTP was verified before allowing password reset
            $otpVerifiedKey = 'pwd_reset_user_' . $result['user']->id;
            if (!Cache::has($otpVerifiedKey)) {
                return $this->errorResponse('Please verify your OTP code first', 422);
            }
            Cache::forget($otpVerifiedKey);

            $result = $this->authService->resetPassword($result['user'], $request->password);
            return $this->successResponse(__('password_reset_successfully'), [
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ]);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function logout(Request $request)
    {
        try {
            $user = auth()->user();

            // Revoke the current token (if using Passport)
            $token = $user->token();
            if ($token) {
                $token->revoke();
            }

            // Delete player_id for this device if provided
            if ($request->filled('player_id')) {
                PlayerId::where('user_id', $user->id)
                    ->where('player_id', $request->player_id)
                    ->delete();
            }

            return $this->successResponse(__('logout_successful'));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function createFreelancer(CreateFreelancerRequest $request)
    {
        try {
            $validated = $request->validated();

            // Step 1: Register the user
            $user = $this->authService->register($validated);

            // ✅ Inject user_id into $validated for use in completeProfile
            $validated['user_id'] = $user['id'];

            // Step 2: Complete freelancer profile
            $this->freelancerService->completeProfile($validated);

            // STEP 3: Send OneSignal welcome notification
            $titles = [
                'en' => __('messages.welcome_freelancer_title', [], 'en'),
                'ar' => __('messages.welcome_freelancer_title', [], 'ar'),
            ];

            $messages = [
                'en' => __('messages.welcome_freelancer_message', [], 'en'),
                'ar' => __('messages.welcome_freelancer_message', [], 'ar'),
            ];

            $this->noticeService->send(
                $user['id'],
                $titles,
                $messages,
                'new_freelancer',
                null,
                false
            );

            $this->metaService->dispatchEvent($user, $request, 'Freelancer Register');

            return $this->successResponse(__('freelancer_created'), new UserResource($user));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function deleteUnverifiedUser(Request $request)
    {
        try {
            $request->validate([
                'phone'  => 'required|string',
                'prefix' => 'required|string',
            ]);

            $result = $this->authService->findByPhoneAndPrefix($request->only('phone', 'prefix'));

            if (!$result['user']) {
                return $this->errorResponse(__('user_not_found'), 404);
            }

            $user = $result['user'];

            if (!is_null($user->verified_at)) {
                return $this->errorResponse(__('user_already_verified'), 422);
            }

            $freelancer = $user->freelancer;

            if ($freelancer) {
                $companyId = $freelancer->company_id;
                $freelancer->forceDelete();

                if ($companyId) {
                    $remainingFreelancers = \App\Models\Freelancer::withTrashed()
                        ->where('company_id', $companyId)
                        ->count();

                    if ($remainingFreelancers === 0) {
                        \App\Models\Company::find($companyId)?->delete();
                    }
                }
            }

            $user->delete();

            return $this->successResponse(__('account_deleted_successfully'));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function deleteAccount(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string',
            ]);

            $user = $request->user();

            if (!Hash::check($request->password, $user->password)) {
                return $this->errorResponse(__('The password is incorrect.'), 422);
            }

            $user->is_active = false;
            $user->save();

            if ($user->freelancer) {
                $serviceIds = $user->services()->pluck('id');
                \App\Models\Request::whereIn('service_id', $serviceIds)->delete();
                $user->services()->delete();
                $user->portfolios()->delete();
            } else {
                $user->requests()->delete();
                $user->quotations()->delete();
            }

            $user->chatsAsUserOne()->update(['user_one_deleted_at' => now(), 'user_two_deleted_at' => now()]);
            $user->chatsAsUserTwo()->update(['user_one_deleted_at' => now(), 'user_two_deleted_at' => now()]);

            $user->delete();

            return $this->successResponse(__('account_deleted_successfully'));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
