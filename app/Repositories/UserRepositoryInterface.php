<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function getUserAuthenticated() : User;
    public function createUser(array $user): User;
    public function attemptLogin(array $credentials): bool;
    public function signout(): void;
    public function resetPassword(array $data, callable $callback);
    public function sendResetLinkEmail(array $data);
}