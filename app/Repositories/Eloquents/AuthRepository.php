<?php

namespace App\Repositories\Eloquents;

use App\Models\PlayerId;
use App\Models\User;
use App\Models\UserLanguage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Services\WhatsAppService;
use App\Utilities\GenerateCode;
use Carbon\Carbon;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Register a new user
     *
     * @param array $data
     * @return User
     */
    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {

            // Normalize phone: strip non-digits and leading zeros
            $localPhone = ltrim(preg_replace('/\D+/', '', $data['phone'] ?? ''), '0');
            $data['phone'] = $localPhone;

            $code = GenerateCode::generate();
            // $code = '123456';

            // 🔹 Check if user exists (even soft deleted)
            $user = User::withTrashed()
                ->where(function ($q) use ($data) {
                    $q->where('email', $data['email'])
                        ->orWhere(function ($q2) use ($data) {
                            $q2->where('prefix', $data['prefix'])
                                ->whereIn('phone', [$data['phone'], '0' . $data['phone']]);
                        });
                })
                ->first();

            if ($user && $user->trashed()) {
                // ✅ Reactivate old account
                $user->restore();
                $user->is_active = true;
                $user->username = $data['username'];
                $user->email = $data['email'];
                $user->prefix = $data['prefix'] ?? null;
                $user->phone = $data['phone'] ?? null;
                $user->gender = $data['gender'] ?? null;
                $user->profession_id = $data['profession_id'] ?? null;
                $user->country_id = $data['country_id'] ?? null;
                $user->password = Hash::make($data['password']);
                $user->avatar = $data['avatar'] ?? null;
                $user->google_id = $data['google_id'] ?? null;
                $user->code = $code;
                $user->code_expires_at = Carbon::now()->addMinutes(10);
                $user->verified_at = null;
                $user->save();

                if ($user->freelancer) {
                    $user->services()->withTrashed()->restore();
                    $user->portfolios()->withTrashed()->restore();
                }

                // 🔹 Reset languages
                if (!empty($data['languages'])) {
                    UserLanguage::where('user_id', $user->id)->delete();
                    foreach ($data['languages'] as $languageId) {
                        UserLanguage::create([
                            'user_id'     => $user->id,
                            'language_id' => $languageId,
                        ]);
                    }
                }
            } elseif (!$user) {
                // ✅ New account
                $user = User::create([
                    'username'      => $data['username'],
                    'email'         => $data['email'],
                    'prefix'        => $data['prefix'] ?? null,
                    'phone'         => $data['phone'] ?? null,
                    'gender'        => $data['gender'] ?? null,
                    'profession_id' => $data['profession_id'] ?? null,
                    'country_id'    => $data['country_id'] ?? null,
                    'password'      => Hash::make($data['password']),
                    'avatar'        => $data['avatar'] ?? null,
                    'code'          => $code,
                    'code_expires_at' => Carbon::now()->addMinutes(10),
                    'google_id'     => $data['google_id'] ?? null,
                    'is_active'     => true,
                ]);

                if (!empty($data['languages'])) {
                    foreach ($data['languages'] as $languageId) {
                        UserLanguage::create([
                            'user_id'     => $user->id,
                            'language_id' => $languageId,
                        ]);
                    }
                }
            }

            // 🔹 Handle player_id
            if (!empty($data['player_id'])) {
                $exists = PlayerId::where('user_id', $user->id)
                    ->where('player_id', $data['player_id'])
                    ->where('platform', $data['platform'])
                    ->exists();

                if (!$exists) {
                    PlayerId::create([
                        'user_id'   => $user->id,
                        'player_id' => $data['player_id'],
                        'platform'  => $data['platform'],
                    ]);
                }
            }

            // 🔹 Send WhatsApp code
            $fullPhoneNumber = $data['prefix'] . $data['phone'];
            $whatsApp = new WhatsAppService();
            $response = $whatsApp->sendTemplateMessage($fullPhoneNumber, $code);

            return $user->load(['profession', 'country', 'languages.language']);
        });
    }


    /**
     * Authenticate user credentials
     *
     * @param array $data
     * @return User|null
     */
    public function login(array $data)
    {

        $localPhone = ltrim(preg_replace('/\D+/', '', $data['phone'] ?? ''), '0');

        $user = User::withTrashed()->with(['profession', 'country', 'languages.language'])
            ->where('prefix', $data['prefix'])
            ->whereIn('phone', [$localPhone, '0' . $localPhone])

            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return null;
        }

        return $user;
    }

    public function findByPhoneAndPrefix($phone, $prefix)
    {
        $localPhone = ltrim(preg_replace('/\D+/', '', $phone ?? ''), '0');

        return User::where('prefix', $prefix)
            ->whereIn('phone', [$localPhone, '0' . $localPhone])
            ->first();
    }

    public function updateCode($user, $code): User
    {
        $user->update([
            'code'            => $code,
            'code_expires_at' => Carbon::now()->addMinutes(10),
        ]);
        return $user->fresh();
    }

    public function sendCodeViaWhatsApp($user, $code): void
    {
        $whatsApp = new WhatsAppService();
        $fullPhoneNumber = $user->prefix . $user->phone;
        $whatsApp->sendTemplateMessage($fullPhoneNumber, $code);
    }

    public function clearCode($user): User
    {
        $user->update([
            'code' => null,
            'verified_at' => Carbon::now()
        ]);
        return $user->load(['profession', 'country', 'languages.language']);
    }
    public function updatePassword($user, $password): User
    {
        $user->update([
            'password' => Hash::make($password)
        ]);
        return $user->load(['profession', 'country', 'languages.language']);
    }
}
