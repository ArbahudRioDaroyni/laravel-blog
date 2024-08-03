<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\{
	AuthController,
};

Route::middleware(['auth', 'verified'])->group(function () {
	Route::get('/', function () {
		return view('welcome');
	});
});

Route::group([
	'prefix' => 'login',
	'middleware' => 'guest'
], function () {
	Route::get('/', [AuthController::class, 'showLoginForm'])
	->name('login');

	Route::post('/', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])
->name('logout')
->middleware('auth');

Route::group([
	'prefix' => 'register',
	'middleware' => 'guest'
], function () {
	Route::get('/', [AuthController::class, 'showRegisterForm'])
	->name('register');

	Route::post('/', [AuthController::class, 'register']);
});

Route::prefix('email')->group(function () {
	Route::post('/verification-notification', [AuthController::class, 'resendVerificationEmail'])
	->middleware(['auth', 'throttle:6,1'])
	->name('verification.send');

	Route::get('/notice', function () {
		return view('auth.verify-email');
	})
	->middleware(['auth'])
	->name('verification.notice');

	Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
		$request->fulfill();
		return redirect('/');
	})
	->middleware(['auth', 'signed'])
	->name('verification.verify');
});

Route::group([
	'prefix' => 'forgot-password',
	'middleware' => 'guest'
], function () {
	Route::get('/', [AuthController::class, 'showForgotPasswordForm'])
	->name('password.request');

	Route::post('/', [AuthController::class, 'sendResetLinkEmail'])
	->name('password.email');
});

Route::group([
	'prefix' => 'reset-password',
	'middleware' => 'guest'
], function () {
	Route::get('/{token}', [AuthController::class, 'showResetForm'])
	->name('password.reset');

	Route::post('/', [AuthController::class, 'resetPassword'])
	->name('password.update');
});