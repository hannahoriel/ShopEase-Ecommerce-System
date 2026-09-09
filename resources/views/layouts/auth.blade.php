<!DOCTYPE html>
<html lang="fil">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ShopEase - @yield('title')</title>

    <!-- Poppins Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    @vite(['resources/css/auth.css'])
</head>

<body>

<div class="card">

    <!-- =====================================================
         LEFT SIDE
    ====================================================== -->
    <div class="left">

        <!-- Decorative Circles -->
        <div class="circle c1"></div>
        <div class="circle c2"></div>
        <div class="circle c3"></div>


        <!-- =================================================
             SHOP EASE LOGO
        ================================================== -->
        <div class="brand">

            <img
                src="{{ asset('icons/login/login-logo.png') }}"
                alt="ShopEase"
                class="login-logo"
            >

        </div>


        <!-- =================================================
             TAGLINE
        ================================================== -->
        <div class="tagline">
            Shop Easier,
            <em>Live Better.</em>
        </div>


        <!-- =================================================
             DESCRIPTION
        ================================================== -->
        <div class="subtext">
            Your favorite products, just a few clicks away.
            Sign in to continue your shopping journey.
        </div>

    </div>


    <!-- =====================================================
         RIGHT SIDE
    ====================================================== -->
    <div class="right">

        @yield('content')

    </div>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

@vite(['resources/js/auth.js'])

@yield('scripts')

</body>
</html>