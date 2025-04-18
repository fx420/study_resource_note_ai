@extends('layouts.index')

@section('title', 'Login - Study Resource Note AI')

{{-- Hide footer on auth pages --}}
@section('hideFooter', true)

{{-- Use auth.css for styles --}}
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">

@section('scripts')
    <script src="{{ asset('js/auth/auth.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <div class="card login-card" style="width: 400px;">
        <h3 class="mb-3 text-center">Sign In</h3>
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required autofocus>
                <small class="validation-error text-danger"></small>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" required>
                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <small class="validation-error text-danger"></small>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-danger btn-login">Login</button>
            </div>
        </form>
        <div class="mt-3 text-center">
            <small>Don't have an account? 
                <a href="{{ route('register') }}" class="register-link text-decoration-none">Register</a>
            </small>
        </div>
    </div>
</div>
@endsection
