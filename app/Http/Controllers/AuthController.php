<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Repositories\UserRepositoryInterface;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    protected $authService;
    protected $userRepository;

    public function __construct(AuthService $authService, UserRepositoryInterface $userRepository)
    {
        $this->authService = $authService;
        $this->userRepository = $userRepository;
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginFormRequest $request): RedirectResponse
    {
        $credentials = $request->safe()->only('email', 'password');

        if ($this->userRepository->isUserAuthenticated($credentials)) {
            session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->userRepository->signout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/login');
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterFormRequest $request): RedirectResponse
    {
        $user = $request->safe()->only(['name', 'email', 'password']);
        $registeredUser = $this->userRepository->createUser($user);
        $registeredUser->sendEmailVerificationNotification();

        return redirect('/')->with('status', 'verification-link-sent');
    }

    public function showVerifiedEmailStatus()
    {
        return $this->userRepository->getUserAuthenticated()->hasVerifiedEmail()
            ? response()->json([
                'message' => 'hasVerifiedEmail',
            ])
            : view('auth.verify-email');
    }

    public function resendVerificationEmail(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect('/')->with('status', 'Your email is already verified.');
        }

        $user->sendEmailVerificationNotification($user);

        return back()->with('status', 'verification-link-sent');
    }

    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetPasswordLinkEmail(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);
        $status = $this->userRepository->sendResetPasswordLinkEmail(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);

        return back()->with('status', 'We have emailed your password reset link!');
    }

    public function showResetPasswordForm($token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $data = $request->safe()->only('email', 'password', 'password_confirmation', 'token');
        $status = $this->userRepository->resetPassword($data);

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
