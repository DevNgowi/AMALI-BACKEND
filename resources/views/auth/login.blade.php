@extends('layouts.guest')

@section('content')
<div class="login-container">
    <div class="card-body login-card-body">
        <p class="login-box-msg">{{ __('Welcome to AMALI') }}</p>
    
        <form action="{{ route('login') }}" method="post">
            @csrf
            <div class="input-group mb-3">
                <input 
                    type="email" 
                    name="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    placeholder="{{ __('Email') }}" 
                    required 
                    autofocus 
                    aria-label="Email"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
                @error('email')
                    <span class="error invalid-feedback">
                        {{ $message }}
                    </span>
                @enderror
            </div>
    
            <div class="input-group mb-3">
                <input 
                    type="password" 
                    name="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    placeholder="{{ __('Password') }}" 
                    required 
                    aria-label="Password"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
                @error('password')
                    <span class="error invalid-feedback">
                        {{ $message }}
                    </span>
                @enderror
            </div>
    
            <div class="row">
                <div class="col-8">
                    <div class="icheck-primary">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>
                <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Login') }}</button>
                </div>
            </div>
        </form>
    
        @if (Route::has('password.request'))
            <p class="mb-1">
                <a href="{{ route('password.request') }}" class="forgot-password">
                    {{ __('Forgot Your Password?') }}
                </a>
            </p>
        @endif
    </div>
</div>

@endsection