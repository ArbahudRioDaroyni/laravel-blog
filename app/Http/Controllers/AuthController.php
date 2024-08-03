<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Mail\VerificationEmail;
use App\Mail\ResetPasswordEmail;
use App\Models\User;
use App\Services\AuthService;

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

	public function login(Request $request)
	{
		$credentials = $request->only('email', 'password');

		if ($this->authService->login($credentials)) {
			$request->session()->regenerate();
			return redirect()->intended('/');
		}

		return back()->withErrors([
			'email' => 'The provided credentials do not match our records.',
		]);
	}

	public function logout(Request $request)
	{
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return redirect('/login');
	}

	public function showRegisterForm()
	{
		return view('auth.register');
	}

	public function register(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|min:8|confirmed',
		]);
		
		$this->authService->register($request->only(['name', 'email', 'password']));

		return redirect('/')->with('status', 'verification-link-sent');
	}

	public function resendVerificationEmail(Request $request)
	{
		$user = $request->user();
		Mail::to($user->email)->send(new VerificationEmail($user));
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

		// if ($status === Password::RESET_LINK_SENT) {
		// 	// Kirim email dengan Mailable kustom
		// 	$token = Password::createToken($request->email);
		// 	Mail::to($request->email)->send(new CustomResetPasswordEmail($token, $request->email));
		// }

		return $status === Password::RESET_LINK_SENT
			? back()->with(['status' => __($status)])
			: back()->withErrors(['email' => __($status)]);
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
