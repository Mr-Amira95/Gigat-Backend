<?php

namespace App\Services;

use Exception;
use App\Utilities\GenerateCode;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\AuthRepositoryInterface;

class AuthService
{
    protected $authRepository;
    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }
    /**
     * Register a new user
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function register(array $data)
    {
        try {
            DB::beginTransaction();
            $user = $this->authRepository->register($data);
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    /**
     * Authenticate user and generate token
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function login(array $data)
    {
        try {
            $user = $this->authRepository->login($data);
            if (!$user || $user->trashed()) {
                throw new Exception(__('invalid_phone_number_or_password'), 401);
            }
            if (!$user->verified_at) {
                return [
                    'user'     => $user,
                    'token'    => null,
                    'verified' => false,
                ];
            }
            if (!$user->is_active) {
                throw new Exception(__('account_is_not_active'), 403);
            }
            $token = $user->createToken('auth_token')->accessToken;
            return [
                'user'     => $user,
                'token'    => $token,
                'verified' => true,
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * Find user by phone and prefix
     *
     * @param string $phone
     * @param string $prefix
     * @return array
     * @throws Exception
     */
    public function findByPhoneAndPrefix(array $data)
    {
        try {
            $user = $this->authRepository->findByPhoneAndPrefix($data['phone'], $data['prefix']);
            return [
                'user' => $user
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * Generate verification code for user
     *
     * @param \App\Models\User $user
     * @return array
     * @throws Exception
     */
    public function generateCode($user)
    {
        try {
            DB::beginTransaction();
            $code = GenerateCode::generate();
            $user = $this->authRepository->updateCode($user, $code);
            DB::commit();

            $this->authRepository->sendCodeViaWhatsApp($user, $code);

            return [
                'code' => $code
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    /**
     * Verify user code
     *
     * @param \App\Models\User $user
     * @return array
     * @throws Exception
     */
    public function verifyCode($user)
    {
        try {
            DB::beginTransaction();
            $user = $this->authRepository->clearCode($user);
            DB::commit();
            $token = $user->createToken('auth_token')->accessToken;
            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    /**
     * Reset user password
     *
     * @param \App\Models\User $user
     * @param string $password
     * @return array
     * @throws Exception
     */
    public function resetPassword($user, string $password)
    {
        try {
            DB::beginTransaction();
            $user = $this->authRepository->updatePassword($user, $password);
            DB::commit();
            $token = $user->createToken('auth_token')->accessToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
