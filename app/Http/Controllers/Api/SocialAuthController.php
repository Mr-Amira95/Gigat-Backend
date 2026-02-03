<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{

    public function redirectToProvider($provider)
    {
        if (!in_array($provider, ['google', 'linkedin'])) {
            return $this->errorResponse('Invalid provider', 400);
        }

        try {
            return $this->successResponse(__('success'), [
                'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
            ]);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }    
    public function handleProviderCallback($provider)
    {
        if (!in_array($provider, ['google', 'linkedin'])) {
            return $this->errorResponse('Invalid provider', 400);
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();

            $user = User::updateOrCreate([
                'email' => $socialUser->getEmail(),
            ], [
                'username' => $socialUser->getName(),
                "{$provider}_id" => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'password' => bcrypt(Str::random(16)), 
                'verified_at' => now(),
            ]);

            $token = $user->createToken(ucfirst($provider) . 'Auth')->accessToken;

            return $this->successResponse(__('success'), [
                'user' => $user,
                'token' => $token,
            ]);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
