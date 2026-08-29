@extends('layouts.auth')

@section('title', 'Log In')

@section('content')
<div class="view active" id="loginView">
  <h1>Welcome!</h1>
  <p class="subhead">Log In to your account</p>

  @if ($errors->any())
    <div class="field-group">
      @foreach ($errors->all() as $error)
        <p style="color:#b02020; font-size:13px; margin-bottom:4px;">{{ $error }}</p>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="field-group">
      <label for="email">Email Address</label>
      <div class="input-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="3.2"/><path d="M5 20c0-3.5 3.1-6 7-6s7 2.5 7 6"/></svg>
        <input type="email" id="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
      </div>
    </div>

    <div class="field-group">
      <label for="loginPass">Password</label>
      <div class="input-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4.5" y="10.5" width="15" height="9.5" rx="2"/><path d="M8 10.5V7.5a4 4 0 018 0v3"/></svg>
        <input type="password" id="loginPass" name="password" placeholder="Enter your password" oninput="onPassInput('loginPass','loginEyeBtn')" required>
        <button type="button" class="toggle-eye" id="loginEyeBtn" onclick="togglePass('loginPass', 'loginEyeBtn')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l18 18"/><path d="M10.6 5.1A10.9 10.9 0 0112 5c5 0 9 4 10 7-0.4 1.2-1.3 2.7-2.6 4M6.7 6.7C4.5 8.1 2.9 10.1 2 12c1 3 5 7 10 7 1.5 0 2.9-.3 4.1-.9"/><path d="M9.9 9.9a3 3 0 004.2 4.2"/></svg>
        </button>
      </div>
      <div class="row-between">
        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="link">Forgot your password?</a>
        @else
          <a href="#" class="link">Forgot your password?</a>
        @endif
      </div>
    </div>

    <button type="submit" class="btn-primary">Log In</button>
  </form>

  <p class="foot-note">New to ShopEase? <a href="{{ route('register') }}" class="link">Sign Up</a></p>
</div>
@endsection
