<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Events\PasswordReset;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function isAuthenticate(array $credentials) : bool
    {
        return $this->userRepository->attemptLogin($credentials);
    }

    public function register(array $request) : User
    {
        $registeredUser = $this->userRepository->createUser($request);
        $this->sendVerificationEmail($registeredUser);

        return $registeredUser;
    }

    public function resendVerificationEmail(User $user): void
    {
        $this->sendVerificationEmail($user);
    }

    public function resetPassword(array $data)
    {
        return $this->userRepository->resetPassword($data, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        });
    }

    public function sendResetLinkEmail(array $data)
    {
        return $this->userRepository->sendResetLinkEmail($data);
    }

    protected function sendVerificationEmail($user) : void
    {
        $user->sendEmailVerificationNotification();
        // Mail::to($user->email)->send(new VerificationEmail($user));
    }
}
