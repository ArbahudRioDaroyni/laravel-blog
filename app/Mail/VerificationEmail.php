<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class VerificationEmail extends Mailable
{
	use Queueable, SerializesModels;

	public $verificationUrl;

	public function __construct(User $user)
	{
		$this->verificationUrl = URL::signedRoute('verification.verify', [
			'id' => $user->id,
			'hash' => sha1($user->email),
		], now()->addMinutes(60));
	}

	public function build()
	{
		return $this->view('emails.verify')
		->with([
				'verificationUrl' => $this->verificationUrl,
		]);
	}
}
