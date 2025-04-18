@extends('layouts.index')

@section('title','My Profile')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('scripts')
  <script src="{{ asset('js/profile.js') }}"></script>
@endsection

@section('content')
<div class="container py-5">
  <div class="card profile-card mx-auto">
    <div class="card-body">

      <h3 class="card-title text-center mb-4 text-white">My Profile</h3>
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <form id="profileForm" action="{{ route('profile.update') }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <!-- Username / Email -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Username</label>
            <div id="usernameDisplay" class="text-white">
                {{ $user->username ?: 'N/A' }}
            </div>
            <input type="text" name="username" id="usernameInput" class="form-control d-none" value="{{ $user->username }}" pattern="^[^\s]+$" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <div id="emailDisplay" class="text-white">{{ $user->email ?: 'N/A'}}</div>
            <input type="email" name="email" id="emailInput" class="form-control d-none" value="{{ $user->email }}" required>
          </div>
        </div>

        <!-- Gender / DOB -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Gender</label>
            <div id="genderDisplay" class="text-white">{{ $user->gender ?: 'N/A'}}</div>
            <div id="genderInput" class="gender-toggle d-none">
              <button type="button" class="btn btn-outline-light" data-value="Male">Male</button>
              <button type="button" class="btn btn-outline-light" data-value="Female">Female</button>
            </div>
            <input type="hidden" name="gender" id="genderValue" value="{{ $user->gender }}">
          </div>

          <div class="col-md-6">
            <label class="form-label">Date of Birth</label>
            <div id="dobDisplay" class="text-white">{{ $user->dob ?: 'N/A'}}</div>
            <input type="date" name="dob" id="dobInput" class="form-control d-none" value="{{ $user->dob }}" required>
          </div>
        </div>

        <!-- University / Course -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">University</label>
            <div id="universityDisplay" class="text-white">{{ $user->university ?: 'N/A'}}</div>
            <select name="university" id="universityInput" class="form-select d-none" required>
              <option value="">Select University</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Course</label>
            <div id="courseDisplay" class="text-white">{{ $user->course ?: 'N/A'}}</div>
            <select name="course" id="courseInput" class="form-select d-none" required>
              <option value="">Select Course</option>
            </select>
          </div>
        </div>

        <!-- Password -->
        <div class="row mb-3">
            <div class="col-md-6">
            <label class="form-label">New Password</label>
            <div id="passwordDisplay" class="text-white">********</div>
            <div class="password-group d-none">
                <input type="password" name="password" id="passwordInput" class="form-control" minlength="6" placeholder="Enter new password">
                <span class="toggle-password">
                <i class="fas fa-eye"></i>
                </span>
            </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-grid gap-2">
          <button type="button" id="editBtn" class="btn btn-outline-light">Edit Profile</button>
          <div id="saveCancelBtns" class="d-none">
            <button type="submit" class="btn btn-success">Save Changes</button>
            <button type="button" class="btn btn-secondary" onclick="cancelEdit()">Cancel</button>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection
