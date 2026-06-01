<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\PlayerId;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use App\Utilities\GenerateCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                if ($user->google_id === $googleUser->getId() || is_null($user->google_id)) {
                    if (is_null($user->google_id)) {
                        $user->google_id = $googleUser->getId();
                        $user->save();
                    }
                    if (is_null($user->verified_at)) {
                        $code = GenerateCode::generate();
                        $key = 'otp_' . $user->prefix . $user->phone;
                        Cache::put($key, $code, now()->addMinutes(5));

                        $user->code = $code;
                        $user->save();

                        $fullPhoneNumber =  $user->prefix . $user->phone;

                        $whatsApp = new WhatsAppService();
                        $response = $whatsApp->sendTemplateMessage($fullPhoneNumber, $code);

                        $prefix = $user->prefix;
                        $phone = $user->phone;

                        return redirect()->route('freelancer.verify.phone', compact('phone', 'prefix'))->with('info', __('please_verify_phone'));
                    }

                        Auth::guard('freelancer')->login($user);

                    return redirect()->route('freelancer.home.index');
                } else {
                    return redirect()->route('freelancer.login')->with('error', 'This email is already associated with another Google account.');
                }
            } else {
                $randomPassword = 'Google@' . Str::random(10);
                session([
                    'google_name'  => $googleUser->getName(),
                    'google_email' => $googleUser->getEmail(),
                    'google_id'    => $googleUser->getId(),
                    'password' => $randomPassword,

                ]);

                return redirect()->route('freelancer.register');
            }
        } catch (\Exception $e) {
            // dd('3');

            return redirect()->route('freelancer.login')->with('error', 'Something went wrong, please try again.');
        }
    }

    public function savePlayerId(Request $request)
    {
        \Log::info('Request received', $request->all());
        $request->validate([
            'player_id' => 'required|string',
            'platform'  => 'required|string'
        ]);

        $user = Auth::guard('freelancer')->user();

        if (!$user) {
            \Log::info('No authenticated freelancer user');
            return response()->json(['status' => 'unauthenticated'], 401);
        }

        \Log::info('Authenticated User: ' . $user->id);

        if ($request->player_id) {
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
                \Log::info('Player ID Created');
            } else {
                \Log::info('Player ID Already Exists');
            }

            return response()->json(['status' => 'success']);
        }

        \Log::info('Missing player_id in request');
        return response()->json(['status' => 'invalid'], 400);
    }
}
