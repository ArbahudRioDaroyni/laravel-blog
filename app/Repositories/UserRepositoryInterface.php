<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function attemptLogin(array $credentials): bool;
		public function createUser(array $request);
}