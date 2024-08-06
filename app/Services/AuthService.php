<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function resetPassword(array $data): string
    {
        return $this->userRepository->resetPassword($data, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        });
    }
}
