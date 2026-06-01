<?php

namespace App\Http\Controllers\Freelancer;

use Illuminate\Http\Request;
use App\Http\Requests\Freelancer\LoginRequest;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Country;
use App\Models\Freelancer;
use App\Models\FreelancerCateogry;
use App\Models\General;
use App\Models\Language;
use App\Models\PlayerId;
use App\Models\Profession;
use App\Models\User;
use App\Models\UserLanguage;
use App\Services\WhatsAppService;
use App\Utilities\FileManager;
use App\Utilities\GenerateCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    // public function showLoginForm()
    // {
    //     $countries = Country::all();
    //     $logo = General::where('key', 'platform_logo')->value('value');

    //     if (Auth::guard('freelancer')->check()) {
    //         return redirect()->route('freelancer.home.index', compact('logo'));
    //     }
    //     return view('pages-freelancer.auth.login', compact('countries', 'logo'));
    // }

    public function showLoginForm(Request $request)
    {
        // dd( $request);
        $countries = Country::all();
        $logo = General::where('key', 'platform_logo')->value('value');

        if ($request->has('token')) {
            // SEC-02: Magic-link token — validate decryption, check active/non-deleted state.
            // TODO: Replace with a DB-backed one-time token that stores expires_at and used_at
            //       to enforce expiry and prevent replay attacks after first use.
            try {
                $freelancerId = decrypt($request->token);
            } catch (\Exception $e) {
                abort(403, 'Invalid or expired link');
            }

            $freelancer = User::withTrashed()->find($freelancerId);

            if (! $freelancer || $freelancer->trashed()) {
                abort(403, 'Account is inactive or has been removed.');
            }

            if (isset($freelancer->is_active) && ! $freelancer->is_active) {
                abort(403, 'Account is deactivated. Please contact support.');
            }

            Auth::shouldUse('freelancer');
            Auth::guard('freelancer')->login($freelancer);
            session()->regenerate();

            return redirect()->route('freelancer.home.index');
        }

        //  If freelancer already logged in normally
        if (Auth::guard('freelancer')->check()) {
            return redirect()->route('freelancer.home.index', compact('logo'));
        }

        return view('pages-freelancer.auth.login', compact('countries', 'logo'));
    }

    public function login(LoginRequest $request)
    {
        $remember = $request->filled('remember');

        $p = $request->input('phone_normalized');

        $freelancer = \App\Models\User::withTrashed()->where('prefix', $request->prefix)
            // ->where('phone', $request->phone)
            ->whereIn('phone', [$p, '0' . $p])
            ->first();

        if (!$freelancer) {
            return back()
                ->withErrors(['phone' => __('credentials_not_match')]) // attach to phone field
                ->withInput($request->only('prefix', 'phone', 'remember'));
        }

        // ✅ check if deleted
        if ($freelancer->trashed()) {
            return back()
                ->withErrors(['phone' => __('account_deleted')])
                ->withInput($request->only('prefix', 'phone', 'remember'));
        }

        if (!Hash::check($request->password, $freelancer->password)) {
            return back()
                ->withErrors(['phone' => __('credentials_not_match')])
                ->withInput($request->only('prefix', 'phone', 'remember'));
        }

        if (is_null($freelancer->verified_at)) {
            $code = GenerateCode::generate();
            // $code = '123456';
            $key = 'otp_' . $request->prefix . $request->phone;
            Cache::put($key, $code, now()->addMinutes(5));

            $freelancer->code = $code;
            $freelancer->save();

            $fullPhoneNumber =  $request->prefix . $request->phone;

            $whatsApp = new WhatsAppService();
            $response = $whatsApp->sendTemplateMessage($fullPhoneNumber, $code);
            // ممكن تبعت SMS هنا لو عندك API
            // SmsHelper::send($freelancer->prefix.$freelancer->phone, "Your verification code is $otpCode");


            $prefix = $request->prefix;
            $phone = $request->phone;

            if ($request->input('player_id')) {
                $exists = PlayerId::where('user_id', $freelancer->id)
                    ->where('player_id', $request->player_id)
                    ->where('platform', $request->platform)
                    ->exists();

                if (!$exists) {
                    PlayerId::create([
                        'user_id'   => $freelancer->id,
                        'player_id' => $request->player_id,
                        'platform'  => $request->platform,
                    ]);
                }
            }


            return redirect()->route('freelancer.verify.phone', compact('phone', 'prefix'))->with('info', __('please_verify_phone'));
        }

        // تسجيل الدخول
        Auth::guard('freelancer')->login($freelancer, $remember);

        $request->session()->regenerate();

        // dd($request->input('player_id') , $request->player_id);
        if ($request->input('player_id')) {
            $exists = PlayerId::where('user_id', $freelancer->id)
                ->where('player_id', $request->player_id)
                ->where('platform', $request->platform)
                ->exists();

            if (!$exists) {
                PlayerId::create([
                    'user_id'   => $freelancer->id,
                    'player_id' => $request->player_id,
                    'platform'  => $request->platform,
                ]);
            }
        }



        return redirect()
            ->intended(route('freelancer.home.index'))
            ->with('success', __('welcome_back'));
    }



    public function logout(Request $request)
    {

        if ($request->filled('player_id')) {
            PlayerId::where('user_id', auth()->user()->id)
                ->where('player_id', $request->player_id)
                ->delete();
        }
        Auth::guard('freelancer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();


        return redirect()
            ->route('freelancer.login')
            ->with('success', __('logout_success'));
    }






    public function showRegisterForm()
    {
        $countries = Country::all();
        $professions = Profession::with('translations')->get();
        $languages = Language::all();
        $categories = Category::all();

        return view('pages-freelancer.auth.register', compact('countries', 'professions', 'languages', 'categories'));
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'avatar' => 'required|image|max:2048',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'prefix' => 'required|string|max:10',
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9]+$/',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = \App\Models\User::where('phone', $value)
                        ->where('prefix', $request->prefix)
                        ->whereNull('deleted_at') // only check active users
                        ->exists();

                    if ($exists) {
                        $fail(__('unique_with_prefix'));
                    }
                }
            ],
            'gender' => 'required|in:male,female',
            'profession_id' => 'required|exists:professions,id',
            'bio' => 'required|string|max:2000',
            'country_id' => 'required|exists:countries,id',
            'google_id' => 'nullable',
            'languages' => 'required|array',
            'languages.*' => 'exists:languages,id',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'file.*'        => 'nullable|file|max:4096',
            'description.*' => [
                'nullable',
                'string',
                'max:255',
            ]
        ]);

        $localPhone = ltrim(preg_replace('/\D+/', '', $validated['phone'] ?? ''), '0');
        $validated['phone'] = $localPhone;
        // check if user exists (even soft-deleted)
        $user = User::withTrashed()
            ->where('email', $validated['email'])
            ->orWhere(function ($q) use ($validated) {
                $q->where('prefix', $validated['prefix'])
                    ->whereIn('phone', [$validated['phone'], '0' . $validated['phone']]);
            })
            ->first();


        $translator = new \App\Utilities\GoogleTranslator();
        $bioTranslations = $translator->translateForStorage($validated['bio']);

        if ($user && $user->trashed()) {
            // 🔹 Reactivate old account
            $user->restore();
            $user->is_active = true;
            $user->username = $validated['username'];
            $user->email = $validated['email'];
            $user->prefix = $validated['prefix'];
            $user->phone = $validated['phone'];
            $user->gender = $validated['gender'];
            $user->profession_id = $validated['profession_id'];
            $user->country_id = $validated['country_id'];
            $user->google_id = $validated['google_id'] ?? null;
            $user->password = bcrypt($validated['password']);
            $user->save();


            // restore freelancer profile
            // if ($user->freelancer) {
            //     $user->freelancer()->restore();
            //     $user->freelancer->update(['bio' => $validated['bio'] ?? null]);
            // } else {
            //     $user->freelancer()->create(['bio' => $validated['bio'] ?? null]);
            // }

            // Restore or create freelancer profile
            if ($user->freelancer) {
                $user->freelancer()->restore();
                $freelancer = $user->freelancer;
            } else {
                $freelancer = $user->freelancer()->create([
                    'status' => 'unverified',
                ]);
            }

            foreach ($bioTranslations as $lang => $text) {
                $freelancer->translations()->updateOrCreate(
                    ['language' => $lang],
                    ['bio' => $text]
                );
            }



            // restore services & portfolios
            $user->services()->withTrashed()->restore();
            $user->portfolios()->withTrashed()->restore();

            // reset languages
            UserLanguage::where('user_id', $user->id)->delete();
            foreach ($validated['languages'] as $langId) {
                UserLanguage::create([
                    'user_id' => $user->id,
                    'language_id' => $langId,
                ]);
            }

            // reset categories
            FreelancerCateogry::where('user_id', $user->id)->delete();
            foreach ($validated['category_ids'] as $catId) {
                FreelancerCateogry::create([
                    'user_id' => $user->id,
                    'category_id' => $catId,
                ]);
            }
        } elseif (!$user) {
            // 🔹 New user
            $user = new User();
            $user->username = $validated['username'];
            $user->email = $validated['email'];
            $user->prefix = $validated['prefix'];
            $user->phone = $validated['phone'];
            $user->gender = $validated['gender'];
            $user->profession_id = $validated['profession_id'];
            $user->country_id = $validated['country_id'];
            $user->google_id = $validated['google_id'] ?? null;
            $user->password = bcrypt($validated['password']);
            $user->is_active = true;
            $user->save();

            session()->forget(['google_name', 'google_email', 'google_id']);

            // avatar
            if ($request->hasFile('avatar')) {
                $avatarPath = FileManager::upload('avatars', $request->file('avatar'));
                $user->avatar = $avatarPath;
                $user->save();
            }

            // freelancer bio
            $freelancer = $user->freelancer()->create(['status' => 'unverified']);

            foreach ($bioTranslations as $lang => $text) {
                $freelancer->translations()->create([
                    'language' => $lang,
                    'bio' => $text,
                ]);
            }
            // languages
            foreach ($validated['languages'] as $langId) {
                UserLanguage::create(['user_id' => $user->id, 'language_id' => $langId]);
            }

            // categories
            foreach ($validated['category_ids'] as $catId) {
                FreelancerCateogry::create(['user_id' => $user->id, 'category_id' => $catId]);
            }
        }

        // certificates
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $index => $file) {
                $description = $request->description[$index] ?? null;
                $path = FileManager::upload('certificates', $file);
                $fileName = pathinfo(strip_tags($file->getClientOriginalName()), PATHINFO_FILENAME);

                $user->certificates()->create([
                    'file_name' => trim($fileName),
                    'file_path' => $path,
                    'description' => $description,
                ]);
            }
        }

        // OTP
        $code = GenerateCode::generate();
        // $code = '123456'; // GenerateCode::generate();
        $key = 'otp_' . $request->prefix . $request->phone;
        Cache::put($key, $code, now()->addMinutes(5));

        $user->code = $code;
        $user->save();

        $fullPhoneNumber = $request->prefix . $request->phone;
        $whatsApp = new WhatsAppService();
        $response = $whatsApp->sendTemplateMessage($fullPhoneNumber, $code);

        $prefix = $request->prefix;
        $phone = $request->phone;

        return redirect()
            ->route('freelancer.verify.phone', compact('phone', 'prefix'))
            ->with('info', __('please_verify_phone'));
    }


    public function showVerifyPhoneForm(Request $request)
    {

        $prefix = $request->query('prefix');
        $phone  = $request->query('phone');

        if (!$prefix || !$phone) {
            return redirect()->route('freelancer.login')->with('error', __('invalid_request'));
        }

        return view('pages-freelancer.auth.verify-phone', compact('prefix', 'phone'));
    }

    public function verifyPhone(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);

        $key = 'otp_' . $request->prefix . $request->phone;
        $cachedCode = Cache::get($key);


        if (!$cachedCode) {
            return back()
                ->withInput($request->only('code'))
                ->withErrors(['code' => __('verification_code_expired')]);
        }

        if ($cachedCode != $request->code) {
            return back()
                ->withInput($request->only('code'))
                ->withErrors(['code' => __('invalid_verification_code')]);
        }


        $user = User::where('prefix', $request->prefix)
            ->where('phone', $request->phone)
            ->first();

        if (!$user) {
            return back()
                ->withErrors(['code' => __('user_not_found')]);
        }


        // dd($request->input('player_id'));
        if ($user->code == $request->code) {
            $user->verified_at = Carbon::now();
            $user->code = null;
            $user->save();
            Cache::forget($key);

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


            Auth::guard('freelancer')->login($user);

            return redirect()->route('freelancer.home.index')
                ->with('success', __('account_verified_successfully'));
        }

        return back()
            ->withInput($request->only('code'))
            ->withErrors(['code' => __('invalid_verification_code')]);
    }


    public function resendPhoneCode(Request $request)
    {
        $request->validate([
            'prefix' => 'required|string',
            'phone'  => 'required|string'
        ]);

        $user = User::where('prefix', $request->prefix)
            ->where('phone', $request->phone)
            ->first();

        $code = GenerateCode::generate();
        // $code = '123455';
        $user->code = $code;
        $user->save();
        $key  = 'otp_' . $request->prefix . $request->phone;

        // خزن الكود الجديد بالكاش
        Cache::put($key, $code, now()->addMinutes(5));


        $fullPhoneNumber = $request->prefix  . $request->phone;
        $whatsApp = new WhatsAppService();
        $response = $whatsApp->sendTemplateMessage($fullPhoneNumber, $code);


        return back()->with('success', __('verification_code_sent_again'));
    }






    private function getTokenIdFromJWT($jwt)
    {
        // decode the token payload (you can use a package like firebase/php-jwt if needed)
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            return null;
        }

        $payload = json_decode(base64_decode($parts[1]), true);

        return $payload['jti'] ?? null;
    }


    public function websiteLogin(Request $request)
    {
        Log::info('Request received for websiteLogin');

        $tokenParam = $request->token;
        Log::info(['tokenParam' => $tokenParam]);

        $tokenString = Token::where('id', $this->getTokenIdFromJWT($tokenParam))
            ->where('revoked', false)
            ->first();

        Log::info('Extracted token', ['token' => $tokenString]);
        Log::info('User', ['user' => $tokenString->user_id]);

        if (!$tokenString) {
            Log::warning('No token found in request');
            return response()->json(['status' => 'error', 'message' => 'Token is required'], 400);
        }

        $freelancer = User::find($tokenString->user_id);
        Log::info('User lookup result', ['freelancer' => $freelancer]);

        if (!$freelancer) {
            Log::warning('User not found for token', ['user_id' => $tokenString->user_id]);
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        Auth::guard('freelancer')->login($freelancer);
        Log::info('User logged in successfully under freelancer guard', ['user_id' => $freelancer->id]);

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $freelancer,
            'redirect_url' => route('freelancer.home.index')
        ]);
    }
}
