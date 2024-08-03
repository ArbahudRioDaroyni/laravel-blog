<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;

class UserRepository implements UserRepositoryInterface
{
    public function attemptLogin(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

		public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Manual action for send email $user->sendEmailVerificationNotification() or event(new Registered($user))
        Mail::to($user->email)->send(new VerificationEmail($user));

        return $user;
    }
}
