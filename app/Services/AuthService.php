<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;

class AuthService
{
	protected $userRepository;

	public function __construct(UserRepositoryInterface $userRepository)
	{
			$this->userRepository = $userRepository;
	}

	public function login(array $credentials): bool
	{
		return $this->userRepository->attemptLogin($credentials);
	}

	public function register(array $data)
	{
		return $this->userRepository->register($data);
	}
}
