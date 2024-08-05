@extends('layouts.app')

@section('content')
@if (session('status') == 'verification-link-sent')
    <div class="alert alert-success" role="alert">
        {{ __('A new verification link has been sent to your email address.') }}
    </div>
@endif
<form method="POST" action="{{ route('login') }}">
    @csrf

    <label for="email">{{ __('E-Mail Address') }}</label>
    <div>
        <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
        @error('email')
            <strong>{{ $message }}</strong>
        @enderror
    </div>

    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
    <div>
        <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
        @error('password')
            <strong>{{ $message }}</strong>
        @enderror
    </div>
    
    <button type="submit" class="btn btn-primary">Login</button>
</form>
@endsection
