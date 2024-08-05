<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Events\PasswordReset;

class UserRepository implements UserRepositoryInterface
{
    public function getUserAuthenticated() : User
    {
        return Auth::user();
    }

    public function createUser(array $user): User
    {
        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            // 'remember_token' => Str::random(60),
        ]);
    }

    public function attemptLogin(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

    public function signout(): void
    {
        Auth::logout();
    }

    public function resetPassword(array $data, callable $callback)
    {
        $status = Password::reset(
            $data,
            function ($user, $password) use ($callback) {
                $callback($user, $password);
            }
        );

        return $status;
    }

    public function sendResetLinkEmail(array $data)
    {
        return Password::sendResetLink($data);
    }
}
