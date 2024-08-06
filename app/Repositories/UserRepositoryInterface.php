<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function getUserAuthenticated() : User;
    public function createUser(array $user): User;
    public function isUserAuthenticated(array $credentials): bool;
    public function signout(): void;
    public function sendResetPasswordLinkEmail(array $data): string;
    public function resetPassword(array $data): string;
}