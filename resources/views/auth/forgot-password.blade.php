@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <label for="email">Email:</label>
    <div>
        <input type="email" id="email" name="email" class="form-control" required>
        @error('email')
            <strong>{{ $message }}</strong>
        @enderror
    </div>
    
    <button type="submit">Kirim</button>
</form>

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
@endsection
