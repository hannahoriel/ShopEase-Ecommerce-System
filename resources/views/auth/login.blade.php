@extends('layouts.auth')

@section('title', 'Log In')

@section('content')
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
                    id="loginEyeBtn"
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

                @if (Route::has('password.request'))

                    <a
                        href="{{ route('password.request') }}"
                        class="link"
                    >
                        Forgot your password?
                    </a>

                @else

                    <a
                        href="#"
                        class="link"
                    >
                        Forgot your password?
                    </a>

                @endif

            </div>

        </div>


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
@endsection