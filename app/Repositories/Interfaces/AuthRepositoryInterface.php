<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface AuthRepositoryInterface
{
    /**
     * Register a new user
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function register(array $data);
    public function login(array $data);

    public function findByPhoneAndPrefix($phone, $prefix = null, $email = null);

    public function updateCode($user,$code);

    public function sendCodeViaWhatsApp($user, $code): void;

    public function clearCode($user);

    public function updatePassword($user,$password);
}
