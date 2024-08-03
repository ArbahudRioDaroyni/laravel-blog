<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\{
	AuthController,
};

Route::middleware(['auth'])->group(function () {
	Route::get('/', function () {
		return view('welcome');
	});
});

Route::middleware(['auth', 'verified'])->group(function () {
	//
});

Route::prefix('login')->middleware('guest')->group(function () {
	Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
	Route::post('/', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('register')->middleware('guest')->group(function () {
	Route::get('/', [AuthController::class, 'showRegisterForm'])->name('register');
	Route::post('/', [AuthController::class, 'register']);
});

Route::prefix('email')->group(function () {
	// 'throttle:6,1'
	Route::post('/verification-notification', [AuthController::class, 'resendVerificationEmail'])
	->middleware(['auth'])->name('verification.send');

	Route::get('/notice', function () {
		return view('auth.verify-email');
	})->middleware(['auth'])->name('verification.notice');

	Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
		$request->fulfill();
		return redirect('/');
	})->middleware(['auth', 'signed'])->name('verification.verify');
});

Route::prefix('forgot-password')->middleware('guest')->group(function () {
	Route::get('/', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
	Route::post('/', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
});

Route::prefix('reset-password')->middleware('guest')->group(function () {
	Route::get('/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
	Route::post('/', [AuthController::class, 'resetPassword'])->name('password.update');
});