<!-- resources/views/auth/register.blade.php -->

@extends('layouts.app')

@section('content')
<h1>{{ __('Register') }}</h1>

<div class="card-body">
    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <label for="name">{{ __('Name') }}</label>
        <div>
            <input id="name" type="text" class="@error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
            @error('name')
                <strong>{{ $message }}</strong>
            @enderror
        </div>

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

        <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>
        <div>
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>

        <button type="submit">
                {{ __('Register') }}
        </button>
    </form>
</div>
@endsection
