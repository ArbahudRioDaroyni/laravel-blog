<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
	protected $authService;

	public function __construct(AuthService $authService)
	{
		$this->authService = $authService;
	}

	public function showLoginForm()
	{
		return view('auth.login');
	}

	public function login(LoginFormRequest $request)
	{
		$credentials = $request->only('email', 'password');

		if ($this->authService->authenticate($credentials)) {
			session()->regenerate();
			return redirect()->intended('/');
		}

		return back()->withErrors([
			'email' => 'The provided credentials do not match our records.',
		]);
	}

	public function logout(Request $request)
	{
		Auth::logout();
		session()->invalidate();
		session()->regenerateToken();

		return redirect('/login');
	}

	public function showRegisterForm()
	{
		return view('auth.register');
	}

	public function register(RegisterFormRequest $request)
	{
		$user = $request->safe()->only(['name', 'email', 'password']);
		$this->authService->register($user);

		return redirect('/')->with('status', 'verification-link-sent');
	}

	public function showEmailNotice()
	{
		return !$request->user()->hasVerifiedEmail() ? view('auth.verify-email') : redirect('/');
	}

	public function resendVerificationEmail(Request $request)
	{
		$user = $request->user();
		$this->authService->resendVerificationEmail($user);

		return back()->with('status', 'verification-link-sent');
	}

	public function showForgotPasswordForm()
	{
		return view('auth.forgot-password');
	}

	public function sendResetLinkEmail(Request $request)
	{
		$request->validate(['email' => 'required|email']);

		$status = Password::sendResetLink(
				$request->only('email')
		);

		return $status === Password::RESET_LINK_SENT
			? back()->with(['status' => __($status)])
			: back()->withErrors(['email' => __($status)]);

		return back()->with('status', 'We have emailed your password reset link!');
	}

	public function showResetForm($token)
	{
		return view('auth.reset-password', ['token' => $token]);
	}

	public function resetPassword(Request $request)
	{
		$request->validate([
			'token' => 'required',
			'email' => 'required|email',
			'password' => 'required|min:8|confirmed',
		]);

		$status = Password::reset(
			$request->only('email', 'password', 'password_confirmation', 'token'),
			function ($user, $password) {
				$user->forceFill([
					'password' => Hash::make($password)
				])->setRememberToken(Str::random(60));

				$user->save();

				event(new PasswordReset($user));
			}
		);

		return $status === Password::PASSWORD_RESET
			? redirect()->route('login')->with('status', __($status))
			: back()->withErrors(['email' => [__($status)]]);
	}
}
