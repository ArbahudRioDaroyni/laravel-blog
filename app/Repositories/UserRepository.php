<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
	public function attemptLogin(array $credentials): bool
	{
		return Auth::attempt($credentials);
	}

	public function createUser(array $request)
	{
		$user = User::create([
			'name' => $request['name'],
			'email' => $request['email'],
			'password' => Hash::make($request['password']),
		]);

		return $user;
	}
}
