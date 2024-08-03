@extends('layouts.app')

@section('content')
@if (session('status') == 'verification-link-sent')
	<div class="alert alert-success" role="alert">
		{{ __('A new verification link has been sent to your email address.') }}
	</div>
@endif
<form method="POST" action="{{ route('login') }}">
	@csrf
	<div class="form-group">
		<label for="email">Email:</label>
		<input type="email" id="email" name="email" class="form-control" required>
	</div>
	<div class="form-group">
		<label for="password">Password:</label>
		<input type="password" id="password" name="password" class="form-control" required>
	</div>
	<button type="submit" class="btn btn-primary">Login</button>
</form>
@endsection
