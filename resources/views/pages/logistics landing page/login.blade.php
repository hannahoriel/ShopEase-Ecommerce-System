@vite(['resources/css/logistics/login.css', 'resources/js/logistics/login.js'])
{{-- ShopEase Logistics login component. Mirrors the original layouts.auth + auth.css login UI. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">


<div class="logistics-auth" data-logistics-login>
    <div class="card">
        <div class="left">
            <div class="circle c1"></div>
            <div class="circle c2"></div>
            <div class="circle c3"></div>
            {{-- Same ShopEase login-logo.png used by the main login design. --}}
            <div class="brand">
                <img class="login-logo" src="{{ asset('icons/login/login-logo.png') }}" alt="ShopEase" loading="eager">
            </div>
            <div class="tagline">Deliver Smarter,<em>Grow Together.</em></div>
            <div class="subtext">Sign in to your logistics company account and continue your ShopEase partnership journey.</div>
        </div>
        <div class="right">
            <div class="view active" id="logisticsLoginView">
                <h1>Welcome!</h1>
                <p class="subhead">Log In to your logistics company account</p>

                @if ($errors->any())
                    <div class="field-group" role="alert">
                        @foreach ($errors->all() as $error)
                            <p style="color:#b02020;font-size:13px;margin-bottom:4px;">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ Route::has('logistics.login.attempt') ? route('logistics.login.attempt') : '#' }}" data-logistics-form data-ready="{{ Route::has('logistics.login.attempt') ? '1' : '0' }}">
                    @csrf
                    <div class="field-group">
                        <label for="logisticsLoginEmail">Company Email Address</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <circle cx="12" cy="8" r="3.2"/>
                                <path d="M5 20c0-3.5 3.1-6 7-6s7 2.5 7 6"/>
                            </svg>
                            <input type="email" id="logisticsLoginEmail" name="email" placeholder="Enter your company email" value="{{ old('email') }}" autocomplete="username" required>
                        </div>
                    </div>
                    <div class="field-group">
                        <label for="logisticsLoginPass">Password</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <rect x="4.5" y="10.5" width="15" height="9.5" rx="2"/>
                                <path d="M8 10.5V7.5a4 4 0 018 0v3"/>
                            </svg>
                            <input type="password" id="logisticsLoginPass" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                            <button type="button" class="toggle-eye" id="logisticsEyeBtn" aria-label="Show password" aria-pressed="false">
                                <img src="{{ asset('icons/login/hide-password.png') }}"
                                     data-show-icon="{{ asset('icons/login/show-password.png') }}"
                                     data-hide-icon="{{ asset('icons/login/hide-password.png') }}"
                                     alt="Password hidden" id="logisticsEyeIcon">
                            </button>
                        </div>
                        <div class="row-between">
                            @if (Route::has('logistics.password.request'))
                                <a href="{{ route('logistics.password.request') }}" class="link">Forgot your password?</a>
                            @else
                                <a href="#" class="link" data-logistics-forgot>Forgot your password?</a>
                            @endif
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">Log In</button>
                    <p class="logistics-form-feedback" data-login-feedback role="status" hidden></p>
                </form>
                <p class="foot-note">New to ShopEase Logistics?
                    <a href="{{ url('/logistics/register/company') }}" class="link" data-logistics-signup>Sign Up</a>
                </p>
            </div>
        </div>
    </div>
</div>

