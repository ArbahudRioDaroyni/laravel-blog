<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;

class AuthService
{
	protected $userRepository;

	public function __construct(UserRepositoryInterface $userRepository)
	{
			$this->userRepository = $userRepository;
	}

	public function authenticate(array $credentials): bool
	{
		return $this->userRepository->attemptLogin($credentials);
	}

	public function register(array $request)
	{
		$registeredUser = $this->userRepository->createUser($request);
		
		// Manual action for send email $user->sendEmailVerificationNotification() or event(new Registered($user))
		Mail::to($registeredUser->email)->send(new VerificationEmail($registeredUser));

		return $registeredUser;
	}

	public function resendVerificationEmail(User $user)
	{
		Mail::to($user->email)->send(new VerificationEmail($user));
	}
}
