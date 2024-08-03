@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('password.update') }}">
	@csrf
	<input type="hidden" name="token" value="{{ $token }}">
	<input type="hidden" name="email" value="{{ $_GET['email'] }}">
	<label for="password">Password:</label>
	<div>
		<input type="password" id="password" name="password" class="form-control" required>
		@error('email')
			<strong>{{ $message }}</strong>
		@enderror
	</div>

	<label for="password-confirm">{{ __('Confirm Password') }}</label>
	<div>
		<input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
	</div>

	<button type="submit">Kirim</button>
</form>
@endsection
