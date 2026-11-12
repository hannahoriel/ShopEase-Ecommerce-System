@extends('layouts.auth')

@section('title', 'Log In')

@section('content')
@if (request()->boolean('setup') && session('first_password'))
    <div class="view active">
        @include('auth.first-password-form')
    </div>
@else
<div class="view active" id="loginView">

    <h1>Welcome!</h1>

    <p class="subhead">
        Log In to your account
    </p>


    @if ($errors->any())

        <div class="field-group">

            @foreach ($errors->all() as $error)

                <p
                    style="
                        color:#b02020;
                        font-size:13px;
                        margin-bottom:4px;
                    "
                >
                    {{ $error }}
                </p>

            @endforeach

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('login.attempt') }}"
    >

        @csrf


        {{-- =====================================================
             EMAIL
        ====================================================== --}}

        <div class="field-group">

            <label for="email">
                Email Address
            </label>

            <div class="input-wrap">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <circle
                        cx="12"
                        cy="8"
                        r="3.2"
                    />

                    <path
                        d="M5 20c0-3.5 3.1-6 7-6s7 2.5 7 6"
                    />
                </svg>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >

            </div>

        </div>


        {{-- =====================================================
             PASSWORD
        ====================================================== --}}

        <div class="field-group">

            <label for="loginPass">
                Password
            </label>

            <div class="input-wrap">

                {{-- Lock icon --}}
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <rect
                        x="4.5"
                        y="10.5"
                        width="15"
                        height="9.5"
                        rx="2"
                    />

                    <path
                        d="M8 10.5V7.5a4 4 0 018 0v3"
                    />
                </svg>


                <input
                    type="password"
                    id="loginPass"
                    name="password"
                    placeholder="Enter your password"
                    oninput="onPassInput('loginPass', 'loginEyeBtn')"
                    required
                >


                {{-- Password visibility button --}}
                <button
                    type="button"
                    class="toggle-eye"
                    data-loading="off" id="loginEyeBtn"
                    onclick="togglePass('loginPass', 'loginEyeBtn')"
                    aria-label="Show password"
                    aria-pressed="false"
                >
                    <img
    src="{{ asset('icons/login/hide-password.png') }}"
    alt="Password hidden"
    id="loginEyeIcon"
>
                </button>

            </div>


            {{-- Forgot Password --}}

            <div class="row-between">

                <button type="button" class="link recovery-link" data-open-recovery>Forgot your password?</button>

            </div>

        </div>


        <label for="rememberLogin" style="display:flex;align-items:center;gap:8px;margin:0 0 18px;font-size:13px;color:#665b57;cursor:pointer;">
            <input type="checkbox" id="rememberLogin" name="remember" value="1" @checked(old('remember', true))>
            Keep me logged in until I log out
        </label>


        {{-- =====================================================
             LOGIN BUTTON
        ====================================================== --}}

        <button
            type="submit"
            class="btn-primary"
        >
            Log In
        </button>

    </form>


    {{-- =====================================================
         SIGN UP
    ====================================================== --}}

    <p class="foot-note">

        New to ShopEase?

        <a
            href="{{ route('register') }}"
            class="link"
        >
            Sign Up
        </a>

    </p>

</div>
@include('auth.landing-password-reset', ['recoveryPortal' => 'main'])
@endif
@endsection
