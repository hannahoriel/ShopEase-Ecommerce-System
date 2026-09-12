<!DOCTYPE html>
<html lang="fil">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ShopEase - @yield('title')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>

<div class="card">
  <div class="left">
    <div class="circle c1"></div>
    <div class="circle c2"></div>
    <div class="circle c3"></div>
    <div class="brand">
      <div class="brand-logo">
        <div class="hamburger"><span></span><span></span><span></span></div>
        <div class="bag-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 8h12l-1 12H7L6 8z"/>
            <path d="M9 8V6a3 3 0 016 0v2"/>
            <path d="M9.5 12a2.5 2 0 005 0" stroke-linecap="round"/>
          </svg>
        </div>
      </div>
      <div class="brand-name">Shop<span>Ease</span></div>
    </div>
    <div class="tagline">
      Shop Easier,
      <em>Live Better.</em>
    </div>
    <div class="subtext">
      Your favorite products, just a few clicks away. Sign in to continue your shopping journey.
    </div>
  </div>

  <div class="right">
    @yield('content')
  </div>
</div>

<script src="{{ asset('js/auth.js') }}"></script>
@yield('scripts')

</body>
</html>
