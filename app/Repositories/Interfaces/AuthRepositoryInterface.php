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

    public function findByPhoneAndPrefix($phone,$prefix);

    public function updateCode($user,$code);

    public function clearCode($user);

    public function updatePassword($user,$password);
}
