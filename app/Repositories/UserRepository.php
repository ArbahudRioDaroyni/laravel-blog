<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class UserRepository implements UserRepositoryInterface
{
    public function getUserAuthenticated() : User
    {
        return Auth::user();
    }

    public function createUser(array $user): User
    {
        return User::create([
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => Hash::make($user['password']),
            // 'remember_token' => Str::random(60),
        ]);
    }

    public function isUserAuthenticated(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

    public function signout(): void
    {
        Auth::logout();
    }

    public function sendResetPasswordLinkEmail(array $data): string
    {
        return Password::sendResetLink($data);
    }

    public function resetPassword(array $data): string
    {
        return Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );
    }
}
