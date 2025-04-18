@extends('layouts.index')

@section('title', 'Register - Study Resource Note AI')

{{-- Hide the footer on auth pages --}}
@section('hideFooter', true)

{{-- Include auth-specific CSS --}}
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">

@section('scripts')
    <script src="{{ asset('js/auth/auth.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <div class="card register-card">
        <h3 class="text-center mb-3">Register</h3>
        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" 
                       pattern="^[^\s]+$" title="Username must not contain spaces." required autofocus>
                <small class="validation-error text-danger"></small>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" 
                       pattern="^[a-zA-Z0-9._%+-]+@(gmail\.com|mail\.com|email\.com)$"
                       title="Email must end with @gmail.com, @mail.com, or @email.com." required>
                <small class="validation-error text-danger"></small>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" 
                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[!@#$%^&*])[A-Za-z\\d!@#$%^&*]{7,}$"
                           title="Password must be more than 6 characters and include at least 1 uppercase letter, 1 lowercase letter, 1 digit, and 1 special character."
                           required>
                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <small class="validation-error text-danger"></small>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    <span class="input-group-text" id="togglePasswordConfirm" style="cursor: pointer;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <small class="validation-error text-danger"></small>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-danger btn-login">Register</button>
            </div>
        </form>
        <div class="mt-3 text-center">
            <small>Already have an account? 
                <a href="{{ route('login') }}" class="register-link text-decoration-none">Login</a>
            </small>
        </div>
    </div>
</div>
@endsection

