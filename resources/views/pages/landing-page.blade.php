<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        ShopEase — Shop Easier, Live Better.
    </title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    @vite(['resources/css/app.css'])


    <style>

        /* =========================================================
           ROOT
        ========================================================== */

        :root {

            --maroon-dark: #52070B;
            --maroon: #8B201A;
            --maroon-mid: #A52D2C;
            --maroon-soft: #C94A45;
            --maroon-light: #A52A2A;

            --peach: #FF876E;
            --peach-light: #FFE8E0;
            --peach-soft: #FFF3F0;

            --cream: #FBF8F6;
            --white: #FFFFFF;

            --text-dark: #161616;
            --text-soft: #454545;
            --text-muted: #858585;

            --border: #EFDCD7;
        }


        /* =========================================================
           RESET
        ========================================================== */

        * {
            box-sizing: border-box;
        }


        html {
            scroll-behavior: smooth;
        }


        body {

            margin: 0;
            padding: 0;

            font-family:
                'Poppins',
                sans-serif;

            background:
                var(--cream);

            color:
                var(--text-dark);

            overflow-x:
                hidden;
        }


        a {

            text-decoration:
                none;

            color:
                inherit;
        }


        button,
        input {

            font-family:
                'Poppins',
                sans-serif;
        }


        /* =========================================================
           LANDING PAGE
        ========================================================== */

        .landing-page {

            min-height:
                100vh;

            position:
                relative;
        }


        /* =========================================================
           NAVBAR
        ========================================================== */

        .landing-navbar {

            position:
                fixed;

            top:
                13px;

            left:
                50%;

            transform:
                translateX(-50%);

            width:
                calc(100% - 230px);

            max-width:
                1240px;

            height:
                53px;

            z-index:
                1000;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            padding:
                0 28px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.90
                );

            backdrop-filter:
                blur(18px);

            -webkit-backdrop-filter:
                blur(18px);

            border:
                1px solid
                rgba(
                    82,
                    7,
                    11,
                    0.05
                );

            border-radius:
                999px;

            box-shadow:
                0 12px 35px
                rgba(
                    82,
                    7,
                    11,
                    0.08
                );
        }


        /* =========================================================
           NAVBAR LOGO
        ========================================================== */

        .landing-logo {

            display:
                flex;

            align-items:
                center;

            justify-content:
                flex-start;

            gap:
                0;

            flex-shrink:
                0;
        }




        .landing-logo-wordmark {

            width:
                130px;

            height:
                auto;

            display:
                block;

            object-fit:
                contain;
        }


        /* =========================================================
           NAV ACTIONS
        ========================================================== */

        .landing-nav-actions {

            display:
                flex;

            align-items:
                center;

            gap:
                9px;
        }


        .login-button {

            min-width:
                78px;

            height:
                34px;

            padding:
                0 16px;

            border:
                1px solid
                #E8CFC9;

            border-radius:
                999px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                13px;

            font-weight:
                600;

            color:
                var(--maroon);

            background:
                #FFFFFF;

            cursor:
                pointer;

            transition:
                all 0.25s ease;
        }


        .login-button:hover {

            background:
                var(--peach-soft);

            transform:
                translateY(-1px);
        }


        .signup-button {

            min-width:
                90px;

            height:
                34px;

            padding:
                0 16px;

            border:
                none;

            border-radius:
                999px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                var(--maroon);

            color:
                #FFFFFF;

            font-size:
                13px;

            font-weight:
                600;

            cursor:
                pointer;

            box-shadow:
                0 8px 18px
                rgba(
                    123,
                    27,
                    27,
                    0.16
                );

            transition:
                all 0.25s ease;
        }


        .signup-button:hover {

            background:
                var(--maroon-light);

            transform:
                translateY(-2px);
        }


        /* =========================================================
           HERO
        ========================================================== */

        .hero {

            position:
                relative;

            min-height:
                calc(
                    100vh - 65px
                );

            padding:
                125px
                8%
                80px;

            display:
                grid;

            grid-template-columns:
                0.88fr
                1.12fr;

            align-items:
                center;

            gap:
                55px;

            overflow:
                hidden;
        }


        /* =========================================================
           HERO BACKGROUND CIRCLES
        ========================================================== */

        .hero-bg-circle {

            position:
                absolute;

            border-radius:
                50%;

            pointer-events:
                none;

            will-change:
                transform;
        }


        .hero-bg-circle.one {

            width:
                175px;

            height:
                175px;

            left:
                -45px;

            top:
                -3px;

            background:
                rgba(
                    255,
                    135,
                    110,
                    0.18
                );

            animation:
                circleFloatA
                10s
                linear
                infinite;
        }


        .hero-bg-circle.two {

            width:
                285px;

            height:
                285px;

            right:
                13%;

            top:
                -104px;

            background:
                rgba(
                    255,
                    135,
                    110,
                    0.13
                );

            animation:
                circleFloatB
                13s
                linear
                infinite;
        }


        .hero-bg-circle.three {

            width:
                150px;

            height:
                150px;

            left:
                -58px;

            bottom:
                -75px;

            background:
                rgba(
                    123,
                    27,
                    27,
                    0.06
                );

            animation:
                circleFloatC
                11s
                linear
                infinite;
        }


        @keyframes circleFloatA {

            0% {
                transform:
                    translate3d(0,0,0);
            }

            25% {
                transform:
                    translate3d(16px,10px,0);
            }

            50% {
                transform:
                    translate3d(30px,25px,0);
            }

            75% {
                transform:
                    translate3d(12px,34px,0);
            }

            100% {
                transform:
                    translate3d(0,0,0);
            }

        }


        @keyframes circleFloatB {

            0% {
                transform:
                    translate3d(0,0,0)
                    scale(1);
            }

            25% {
                transform:
                    translate3d(-15px,18px,0)
                    scale(1.03);
            }

            50% {
                transform:
                    translate3d(-28px,32px,0)
                    scale(1.06);
            }

            75% {
                transform:
                    translate3d(-14px,17px,0)
                    scale(1.03);
            }

            100% {
                transform:
                    translate3d(0,0,0)
                    scale(1);
            }

        }


        @keyframes circleFloatC {

            0% {
                transform:
                    translate3d(0,0,0);
            }

            25% {
                transform:
                    translate3d(12px,-12px,0);
            }

            50% {
                transform:
                    translate3d(24px,-24px,0);
            }

            75% {
                transform:
                    translate3d(10px,-15px,0);
            }

            100% {
                transform:
                    translate3d(0,0,0);
            }

        }


        /* =========================================================
           HERO COPY
        ========================================================== */

        .hero-copy {

            position:
                relative;

            z-index:
                5;

            max-width:
                510px;

            margin-left:
                20px;

            transform:
                translateY(45px);
        }


        .hero-title {

            margin:
                0;

            font-size:
                clamp(
                    58px,
                    5.3vw,
                    78px
                );

            line-height:
                0.98;

            letter-spacing:
                -3px;

            font-weight:
                800;

            color:
                var(--maroon-dark);
        }


        .hero-title span {

            color:
                var(--peach);
        }


        .hero-description {

            max-width:
                480px;

            margin:
                22px 0 0;

            font-size:
                15px;

            line-height:
                1.7;

            color:
                #707070;
        }


        /* =========================================================
           HERO BUTTON
        ========================================================== */

        .hero-actions {

            display:
                flex;

            align-items:
                center;

            gap:
                12px;

            margin-top:
                27px;
        }


        .hero-primary {

            min-width:
                165px;

            height:
                48px;

            gap:
                9px;

            border-radius:
                999px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                var(--maroon);

            color:
                #FFFFFF;

            font-size:
                12px;

            font-weight:
                600;

            box-shadow:
                0 10px 25px
                rgba(
                    123,
                    27,
                    27,
                    0.18
                );

            transition:
                all 0.25s ease;
        }


        .hero-primary:hover {

            transform:
                translateY(-3px);

            background:
                var(--maroon-light);

            box-shadow:
                0 13px 28px
                rgba(
                    123,
                    27,
                    27,
                    0.24
                );
        }


        .hero-primary svg {

            width:
                14px;

            height:
                14px;
        }


        /* =========================================================
           HERO VISUAL
        ========================================================== */

        .hero-visual {

            position:
                relative;

            min-height:
                470px;

            display:
                flex;

            justify-content:
                center;

            align-items:
                center;

            z-index:
                5;

            transform:
                translateY(25px);

            perspective:
                1200px;
        }


        /* =========================================================
           MAIN VISUAL
        ========================================================== */

        .visual-card {

            position:
                relative;

            width:
                560px;

            height:
                350px;

            border-radius:
                34px;

            overflow:
                visible;

            background:
                linear-gradient(
                    135deg,
                    #650E14 0%,
                    #7B1B1B 48%,
                    #942A2A 100%
                );

            box-shadow:
                0 32px 75px
                rgba(
                    82,
                    7,
                    11,
                    0.20
                );

            transform-style:
                preserve-3d;

            transform-origin:
                center center;

            will-change:
                transform;

            backface-visibility:
                hidden;

            -webkit-backface-visibility:
                hidden;
        }


        /* =========================================================
           VISUAL CIRCLES
        ========================================================== */

        .visual-circle {

            position:
                absolute;

            border-radius:
                50%;

            pointer-events:
                none;

            will-change:
                transform;
        }


        .visual-circle.one {

            width:
                210px;

            height:
                210px;

            right:
                -38px;

            top:
                -40px;

            background:
                rgba(
                    255,
                    135,
                    110,
                    0.16
                );

            animation:
                visualCircleOne
                9s
                linear
                infinite;
        }


        .visual-circle.two {

            width:
                175px;

            height:
                175px;

            left:
                -55px;

            bottom:
                -48px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.05
                );

            animation:
                visualCircleTwo
                11s
                linear
                infinite;
        }


        @keyframes visualCircleOne {

            0% {
                transform:
                    translate3d(0,0,0)
                    scale(1);
            }

            25% {
                transform:
                    translate3d(-12px,16px,0)
                    scale(1.03);
            }

            50% {
                transform:
                    translate3d(-25px,30px,0)
                    scale(1.06);
            }

            75% {
                transform:
                    translate3d(-10px,18px,0)
                    scale(1.03);
            }

            100% {
                transform:
                    translate3d(0,0,0)
                    scale(1);
            }

        }


        @keyframes visualCircleTwo {

            0% {
                transform:
                    translate3d(0,0,0)
                    scale(1);
            }

            25% {
                transform:
                    translate3d(12px,-7px,0)
                    scale(1.02);
            }

            50% {
                transform:
                    translate3d(24px,-16px,0)
                    scale(1.04);
            }

            75% {
                transform:
                    translate3d(11px,-8px,0)
                    scale(1.02);
            }

            100% {
                transform:
                    translate3d(0,0,0)
                    scale(1);
            }

        }


        /* =========================================================
           BAG
        ========================================================== */

        .visual-bag {

            position:
                absolute;

            top:
                -50px;

            left:
                50%;

            transform:
                translateX(-50%);

            width:
                400px;

            z-index:
                3;

            animation:
                bagFloat
                4.2s
                ease-in-out
                infinite;
        }


        @keyframes bagFloat {

            0%,
            100% {

                transform:
                    translateX(-50%)
                    translateY(0);
            }

            50% {

                transform:
                    translateX(-50%)
                    translateY(-8px);
            }

        }


        .visual-bag img {

            width:
                100%;

            height:
                auto;

            display:
                block;

            object-fit:
                contain;
        }


        /* =========================================================
           BRAND
        ========================================================== */

        .visual-brand {

            position:
                absolute;

            top:
                80px;

            left:
                50%;

            transform:
                translateX(-50%);

            z-index:
                4;

            width:
                300px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;
        }


        .visual-brand img {

            width:
                100%;

            height:
                auto;

            display:
                block;

            object-fit:
                contain;
        }


        /* =========================================================
           FLOATING PRODUCTS
        ========================================================== */

        .floating-product {

            position:
                absolute;

            width:
                170px;

            min-height:
                145px;

            padding:
                12px;

            border-radius:
                18px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.98
                );

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    0.85
                );

            box-shadow:
                0 20px 40px
                rgba(
                    0,
                    0,
                    0,
                    0.13
                );

            z-index:
                10;

            transition:
                box-shadow 0.25s ease;

            will-change:
                transform;
        }


        .floating-product:hover {

            box-shadow:
                0 24px 45px
                rgba(
                    0,
                    0,
                    0,
                    0.17
                );
        }


        .floating-product.left {

            left:
                -15px;

            top:
                -35px;

            animation:
                productLeft
                5.2s
                linear
                infinite;
        }


        .floating-product.watch {

            width:
                145px;

            top:
                -55px;

            right:
                48px;

            animation:
                productWatch
                5.6s
                linear
                infinite;
        }


        .floating-product.shoes {

            width:
                155px;

            right:
                -68px;

            bottom:
                24px;

            animation:
                productShoes
                5.8s
                linear
                infinite;
        }


        @keyframes productLeft {

            0% {
                transform:
                    translate3d(0,0,0)
                    rotate(-5deg);
            }

            25% {
                transform:
                    translate3d(3px,-5px,0)
                    rotate(-3.5deg);
            }

            50% {
                transform:
                    translate3d(5px,-9px,0)
                    rotate(-2deg);
            }

            75% {
                transform:
                    translate3d(2px,-4px,0)
                    rotate(-3.5deg);
            }

            100% {
                transform:
                    translate3d(0,0,0)
                    rotate(-5deg);
            }

        }


        @keyframes productWatch {

            0% {
                transform:
                    translate3d(0,0,0)
                    rotate(5deg);
            }

            25% {
                transform:
                    translate3d(-2px,-6px,0)
                    rotate(3.5deg);
            }

            50% {
                transform:
                    translate3d(-4px,-10px,0)
                    rotate(2deg);
            }

            75% {
                transform:
                    translate3d(-2px,-5px,0)
                    rotate(3.5deg);
            }

            100% {
                transform:
                    translate3d(0,0,0)
                    rotate(5deg);
            }

        }


        @keyframes productShoes {

            0% {
                transform:
                    translate3d(0,0,0)
                    rotate(-6deg);
            }

            25% {
                transform:
                    translate3d(-2px,5px,0)
                    rotate(-4.5deg);
            }

            50% {
                transform:
                    translate3d(-4px,10px,0)
                    rotate(-3deg);
            }

            75% {
                transform:
                    translate3d(-2px,5px,0)
                    rotate(-4.5deg);
            }

            100% {
                transform:
                    translate3d(0,0,0)
                    rotate(-6deg);
            }

        }


        /* =========================================================
           PRODUCT VISUAL
        ========================================================== */

        .product-photo {

            height:
                72px;

            margin-bottom:
                8px;

            border-radius:
                12px;

            overflow:
                hidden;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                linear-gradient(
                    135deg,
                    #FFE4DD,
                    #FFF7F4
                );
        }


        .product-photo svg {

            width:
                52px;

            height:
                52px;
        }


        .product-name {

            margin:
                0;

            font-size:
                10px;

            line-height:
                1.25;

            font-weight:
                600;

            color:
                #222222;
        }


        .product-price {

            margin:
                3px 0 0;

            font-size:
                10px;

            font-weight:
                600;

            color:
                var(--maroon);
        }


        .product-stars {

            margin-top:
                3px;

            font-size:
                8px;

            letter-spacing:
                1px;

            color:
                var(--peach);
        }


        /* =========================================================
           HAPPY SHOPPERS
        ========================================================== */

        .visual-stat {

            position:
                absolute;

            left:
                -40px;

            bottom:
                -18px;

            width:
                180px;

            padding:
                12px 13px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.97
                );

            border-radius:
                15px;

            box-shadow:
                0 20px 34px
                rgba(
                    0,
                    0,
                    0,
                    0.13
                );

            z-index:
                11;

            animation:
                statFloat
                4.8s
                ease-in-out
                infinite;
        }


        @keyframes statFloat {

            0%,
            100% {
                transform:
                    translateY(0);
            }

            50% {
                transform:
                    translateY(-7px);
            }

        }


        .visual-stat-top {

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                8px;
        }


        .visual-stat-label {

            display:
                block;

            font-size:
                8px;

            color:
                #909090;
        }


        .visual-stat-value {

            margin-top:
                1px;

            font-size:
                18px;

            line-height:
                1;

            font-weight:
                700;

            color:
                var(--maroon-dark);
        }


        .visual-stat-change {

            padding:
                4px 7px;

            border-radius:
                999px;

            background:
                #EAF6E9;

            color:
                #2F7D31;

            font-size:
                7px;

            font-weight:
                600;
        }


        .visual-stat-line {

            height:
                5px;

            margin-top:
                10px;

            border-radius:
                999px;

            background:
                #F1E5E1;

            overflow:
                hidden;
        }


        .visual-stat-progress {

            width:
                76%;

            height:
                100%;

            border-radius:
                inherit;

            background:
                linear-gradient(
                    90deg,
                    var(--peach),
                    var(--maroon-light)
                );

            animation:
                progressLoop
                3.2s
                ease-in-out
                infinite alternate;
        }


        @keyframes progressLoop {

            from {
                width:
                    63%;
            }

            to {
                width:
                    84%;
            }

        }


        /* =========================================================
           HEART / CART
        ========================================================== */

        .floating-circle {

            position:
                absolute;

            width:
                45px;

            height:
                45px;

            border-radius:
                50%;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                linear-gradient(
                    145deg,
                    #F06456,
                    #C93832
                );

            border:
                2px solid
                rgba(
                    255,
                    255,
                    255,
                    0.70
                );

            box-shadow:
                0 10px 22px
                rgba(
                    123,
                    27,
                    27,
                    0.22
                );

            z-index:
                12;

            color:
                #FFFFFF;

            animation:
                circleFloat
                4.5s
                linear
                infinite;
        }


        .floating-circle svg {

            width:
                17px;

            height:
                17px;
        }


        .floating-circle.heart-one {

            right:
                90px;

            top:
                40px;
        }


        .floating-circle.heart-two {

            right:
                -42px;

            top:
                75px;

            width:
                40px;

            height:
                40px;
        }


        .floating-circle.cart {

            right:
                75px;

            bottom:
                8px;
        }


        @keyframes circleFloat {

            0% {
                transform:
                    translate3d(0,0,0);
            }

            25% {
                transform:
                    translate3d(0,-4px,0);
            }

            50% {
                transform:
                    translate3d(0,-8px,0);
            }

            75% {
                transform:
                    translate3d(0,-4px,0);
            }

            100% {
                transform:
                    translate3d(0,0,0);
            }

        }


        /* =========================================================
           CATEGORY STRIP
        ========================================================== */

        .category-strip {

            position:
                relative;

            z-index:
                20;

            background:
                var(--maroon-dark);

            overflow:
                hidden;

            min-height:
                65px;

            display:
                flex;

            align-items:
                center;
        }


        .category-track {

            width:
                max-content;

            display:
                flex;

            align-items:
                center;

            gap:
                38px;

            padding:
                0 42px;

            animation:
                categoryMarquee
                46s
                linear
                infinite;

            will-change:
                transform;
        }


        .category-item {

            display:
                flex;

            align-items:
                center;

            gap:
                9px;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.92
                );

            font-size:
                11px;

            font-weight:
                500;

            white-space:
                nowrap;
        }


        .category-icon {

            width:
                22px;

            height:
                22px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            color:
                var(--peach);

            flex-shrink:
                0;
        }


        .category-icon svg {

            width:
                21px;

            height:
                21px;
        }


        .category-divider {

            color:
                var(--peach);

            font-size:
                10px;

            flex-shrink:
                0;
        }


        @keyframes categoryMarquee {

            from {
                transform:
                    translateX(0);
            }

            to {
                transform:
                    translateX(-50%);
            }

        }


        /* =========================================================
           GUEST SHOP
        ========================================================== */

        .guest-shop-section {

            width:
                100%;

            padding:
                80px
                7%
                95px;

            background:
                var(--cream);

            scroll-margin-top:
                30px;
        }


        .guest-shop-heading {

            max-width:
                760px;

            margin:
                0 auto 38px;

            text-align:
                center;
        }


        .guest-shop-label {

            display:
                inline-block;

            margin-bottom:
                8px;

            font-size:
                11px;

            font-weight:
                700;

            letter-spacing:
                1.8px;

            text-transform:
                uppercase;

            color:
                var(--peach);
        }


        .guest-shop-heading h2 {

            margin:
                0;

            font-size:
                38px;

            line-height:
                1.15;

            font-weight:
                700;

            color:
                var(--maroon-dark);
        }


        .guest-shop-heading p {

            max-width:
                580px;

            margin:
                12px auto 0;

            font-size:
                13px;

            line-height:
                1.7;

            color:
                #777777;
        }


        /* =========================================================
           SEARCH
        ========================================================== */

        .guest-search-wrapper {

            width:
                min(
                    100%,
                    760px
                );

            margin:
                0 auto 45px;

            position:
                relative;
        }


        .guest-search {

            width:
                100%;

            height:
                58px;

            padding:
                0 58px
                0 22px;

            border:
                1px solid
                #E8D2CC;

            border-radius:
                999px;

            outline:
                none;

            background:
                #FFFFFF;

            color:
                var(--text-dark);

            font-size:
                13px;

            box-shadow:
                0 12px 30px
                rgba(
                    82,
                    7,
                    11,
                    0.06
                );

            transition:
                border-color 0.25s ease,
                box-shadow 0.25s ease;
        }


        .guest-search::placeholder {
            color:
                #A0A0A0;
        }


        .guest-search:focus {

            border-color:
                var(--peach);

            box-shadow:
                0 14px 32px
                rgba(
                    123,
                    27,
                    27,
                    0.09
                );
        }


        .guest-search-icon {

            position:
                absolute;

            right:
                21px;

            top:
                50%;

            width:
                19px;

            height:
                19px;

            transform:
                translateY(-50%);

            color:
                var(--maroon);

            pointer-events:
                none;
        }


        /* =========================================================
           PRODUCT GRID
        ========================================================== */

        .guest-products {

            max-width:
                1150px;

            margin:
                0 auto;

            display:
                grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap:
                22px;
        }


        /* =========================================================
           PRODUCT CARD
        ========================================================== */

        .guest-product-card {

            background:
                #FFFFFF;

            border:
                1px solid
                #EDDAD5;

            border-radius:
                20px;

            overflow:
                hidden;

            box-shadow:
                0 12px 30px
                rgba(
                    82,
                    7,
                    11,
                    0.045
                );

            transition:
                transform 0.3s ease,
                box-shadow 0.3s ease,
                border-color 0.3s ease;
        }


        .guest-product-card:hover {

            transform:
                translateY(-6px);

            border-color:
                #E8C2B9;

            box-shadow:
                0 20px 38px
                rgba(
                    82,
                    7,
                    11,
                    0.10
                );
        }


        .guest-product-image {

            height:
                210px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                linear-gradient(
                    135deg,
                    #FFF0EA,
                    #FFF8F5
                );

            position:
                relative;
        }


        .guest-product-image svg {

            width:
                86px;

            height:
                86px;

            color:
                var(--maroon);
        }

        .guest-product-image img {

            width:
                100%;

            height:
                100%;

            display:
                block;

            object-fit:
                cover;

            object-position:
                center;

            transition:
                transform 0.45s ease,
                filter 0.45s ease;
        }


        .guest-product-card:hover .guest-product-image img {

            transform:
                scale(1.045);

            filter:
                saturate(1.04);
        }


        .guest-product-badge {

            position:
                absolute;

            top:
                13px;

            left:
                13px;

            padding:
                5px 9px;

            border-radius:
                999px;

            background:
                var(--peach);

            color:
                var(--maroon-dark);

            font-size:
                8px;

            font-weight:
                700;
        }


        .guest-product-content {

            padding:
                17px 17px 18px;
        }


        .guest-product-category {

            margin:
                0 0 6px;

            font-size:
                8px;

            text-transform:
                uppercase;

            letter-spacing:
                1px;

            font-weight:
                700;

            color:
                var(--peach);
        }


        .guest-product-name {

            margin:
                0;

            font-size:
                14px;

            line-height:
                1.3;

            font-weight:
                700;

            color:
                #222222;
        }


        .guest-product-rating {

            display:
                flex;

            align-items:
                center;

            gap:
                5px;

            margin-top:
                8px;
        }


        .guest-product-stars {

            font-size:
                10px;

            letter-spacing:
                1px;

            color:
                var(--peach);
        }


        .guest-product-rating-number {

            font-size:
                9px;

            color:
                #858585;
        }


        .guest-product-bottom {

            margin-top:
                14px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                10px;
        }


        .guest-product-price {

            font-size:
                16px;

            line-height:
                1;

            font-weight:
                700;

            color:
                var(--maroon);
        }


        .guest-product-button {

            height:
                34px;

            padding:
                0 12px;

            border:
                none;

            border-radius:
                999px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                var(--peach-soft);

            color:
                var(--maroon);

            font-size:
                9px;

            font-weight:
                700;

            cursor:
                pointer;

            transition:
                all 0.25s ease;
        }


        .guest-product-button:hover {

            background:
                var(--peach);

            color:
                var(--maroon-dark);

            transform:
                translateY(-1px);
        }


        /* =========================================================
           NO RESULTS
        ========================================================== */

        .guest-no-results {

            display:
                none;

            text-align:
                center;

            padding:
                45px 20px;

            color:
                #777777;

            font-size:
                13px;
        }


        .guest-no-results strong {

            display:
                block;

            margin-bottom:
                5px;

            color:
                var(--maroon-dark);

            font-size:
                17px;
        }


        /* =========================================================
           EASE SECTION
           WHY SHOPEASE — LIGHT PEACH THEME
        ========================================================== */

        .ease-section {

            position: relative;
            overflow: hidden;
            padding: 100px 7% 32px;

            background:
                radial-gradient(circle at 12% 18%, rgba(255,255,255,0.72), transparent 34%),
                radial-gradient(circle at 88% 82%, rgba(255,135,110,0.14), transparent 38%),
                linear-gradient(135deg, #f9e9e1 0%, #f4c4b4 48%, #f8d1c4 100%);

            color: var(--text-dark);
        }


        .ease-blob {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(1px);
            opacity: 0.8;
        }


        .ease-blob.a {
            width: 320px;
            height: 320px;
            left: -110px;
            top: -90px;
            background:
                radial-gradient(circle, rgba(255,255,255,0.58), transparent 70%);
            animation: circleFloatA 14s linear infinite;
        }


        .ease-blob.b {
            width: 280px;
            height: 280px;
            right: -90px;
            bottom: -110px;
            background:
                radial-gradient(circle, rgba(255,135,110,0.16), transparent 70%);
            animation: circleFloatC 16s linear infinite;
        }


        .ease-inner {
            position: relative;
            z-index: 2;
            max-width: 1200px;
            margin: 0 auto;
        }


        .ease-heading {
            max-width: 680px;
            margin: 0 auto 56px;
            text-align: center;

            opacity: 0;
            transform: translateY(26px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }


        .ease-heading.in-view {
            opacity: 1;
            transform: translateY(0);
        }


        .ease-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            font-size: 12px;
            font-weight: 700;
            color: var(--maroon);
            letter-spacing: 0.25px;
        }


        .ease-label::before {
            content: "";
            width: 26px;
            height: 1px;
            background: var(--peach);
        }


        .ease-heading h2 {
            margin: 0;
            font-size: 36px;
            line-height: 1.2;
            font-weight: 700;
            color: var(--maroon-dark);
        }


        .ease-heading p {
            max-width: 520px;
            margin: 14px auto 0;
            font-size: 13.5px;
            line-height: 1.75;
            color: #756D6B;
        }


        /* FEATURE GRID */

        .ease-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
            margin-bottom: 60px;
        }


        .ease-card {
            position: relative;
            padding: 28px 24px 26px;
            border-radius: 22px;

            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(123,27,27,0.08);

            box-shadow:
                0 15px 34px rgba(123,27,27,0.07);

            backdrop-filter: blur(7px);
            -webkit-backdrop-filter: blur(7px);

            opacity: 0;
            transform: translateY(30px);

            transition:
                opacity 0.7s ease,
                transform 0.7s ease,
                background 0.3s ease,
                border-color 0.3s ease,
                box-shadow 0.3s ease;
        }


        .ease-card.in-view {
            opacity: 1;
            transform: translateY(0);
        }


        .ease-card:hover {
            background: #FFFFFF;
            border-color: rgba(165,42,42,0.18);
            box-shadow: 0 22px 44px rgba(123,27,27,0.11);
            transform: translateY(-5px);
        }


        .ease-card-icon {
            width: 50px;
            height: 50px;
            margin-bottom: 18px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(145deg, var(--maroon), var(--peach));

            color: #FFFFFF;
            box-shadow: 0 10px 22px rgba(123,27,27,0.15);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }


        .ease-card:hover .ease-card-icon {
            transform: translateY(-3px) rotate(-6deg);
            box-shadow: 0 14px 26px rgba(123,27,27,0.19);
        }


        .ease-card-icon svg {
            width: 24px;
            height: 24px;
        }


        .ease-card h3 {
            margin: 0 0 8px;
            font-size: 16px;
            font-weight: 700;
            color: var(--maroon-dark);
        }


        .ease-card p {
            margin: 0;
            font-size: 12.5px;
            line-height: 1.7;
            color: #756D6B;
        }


        /* LIVE TRACKING PANEL */

        .ease-tracking {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 50px;
            padding: 44px;
            border-radius: 28px;

            background:
                linear-gradient(135deg, rgba(255,255,255,0.92), rgba(255,248,245,0.86));

            border: 1px solid rgba(123,27,27,0.08);
            box-shadow: 0 20px 45px rgba(123,27,27,0.08);

            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }


        .ease-tracking.in-view {
            opacity: 1;
            transform: translateY(0);
        }


        .ease-tracking-copy .ease-label {
            margin-bottom: 12px;
        }


        .ease-tracking-copy h3 {
            margin: 0 0 12px;
            font-size: 25px;
            line-height: 1.28;
            font-weight: 700;
            color: var(--maroon-dark);
        }


        .ease-tracking-copy p {
            margin: 0 0 24px;
            font-size: 13px;
            line-height: 1.75;
            color: #756D6B;
            max-width: 400px;
        }


        .ease-stats-row {
            display: flex;
            gap: 26px;
            flex-wrap: wrap;
        }


        .ease-stat {
            min-width: 90px;
        }


        .ease-stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--maroon);
            line-height: 1;
        }


        .ease-stat-label {
            display: block;
            margin-top: 6px;
            font-size: 10.5px;
            color: #8C817E;
        }


        /* PARCEL TRACKER MOCK CARD */

        .ease-parcel-card {
            position: relative;
            padding: 26px 24px;
            border-radius: 20px;
            background:
                linear-gradient(145deg, #4A080C, #650E14 55%, #791D1D 100%);
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: 0 30px 60px rgba(82,7,11,0.24);
        }


        .ease-parcel-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }


        .ease-parcel-id {
            font-size: 10px;
            letter-spacing: 0.5px;
            color: rgba(255,255,255,0.58);
        }


        .ease-parcel-live {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 999px;
            background: rgba(255,135,110,0.15);
            color: var(--peach);
            font-size: 9.5px;
            font-weight: 700;
        }


        .ease-parcel-live-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--peach);
            animation: pulseDot 1.4s ease-in-out infinite;
        }


        .ease-parcel-route {
            position: relative;
            padding-left: 6px;
        }


        .ease-parcel-line {
            position: absolute;
            left: 15px;
            top: 9px;
            height: 0;
            width: 2px;
            background: rgba(255,255,255,0.15);
            border-radius: 999px;
        }


        .ease-parcel-progress-line {
            position: absolute;
            left: 15px;
            top: 9px;
            height: 0;
            width: 2px;
            background: linear-gradient(180deg, var(--peach), #F7B0A0);
            border-radius: 999px;
            transform: none;
            transform-origin: top center;
            will-change: height;
        }


        /* =========================================================
           TRACKING ANIMATIONS
        ========================================================== */

        @keyframes pulseDot {
            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255,135,110,0.32);
            }

            50% {
                transform: scale(1.18);
                box-shadow: 0 0 0 6px rgba(255,135,110,0);
            }
        }


        /* =========================================================
           TRACKING STATE
        ========================================================== */

        .ease-parcel-step.delivered-reached .ease-parcel-dot {
            background: var(--peach);
            border-color: #FFFFFF;
            box-shadow: 0 0 0 5px rgba(255,135,110,0.16);
            transform: scale(1.08);
            animation: deliveredPulse 0.8s ease-out;
        }


        .ease-parcel-step.delivered-reached .ease-parcel-step-title {
            color: #FFFFFF;
            transition: color 0.25s ease;
        }


        .ease-parcel-step.delivered-reached .ease-parcel-step-time {
            color: rgba(255,255,255,0.58);
            transition: color 0.25s ease;
        }


        @keyframes deliveredPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255,135,110,0);
                transform: scale(0.96);
            }

            55% {
                box-shadow: 0 0 0 7px rgba(255,135,110,0.20);
                transform: scale(1.14);
            }

            100% {
                box-shadow: 0 0 0 5px rgba(255,135,110,0.16);
                transform: scale(1.08);
            }
        }


        .ease-parcel-step {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding-bottom: 22px;
        }


        .ease-parcel-step:last-child {
            padding-bottom: 0;
        }


        .ease-parcel-dot {
            position: relative;
            z-index: 2;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: rgba(255,255,255,0.16);
            border: 2px solid rgba(255,255,255,0.30);
            flex-shrink: 0;
            margin-top: 2px;
        }


        .ease-parcel-step.done .ease-parcel-dot {
            background: var(--peach);
            border-color: var(--peach);
        }


        .ease-parcel-step.active .ease-parcel-dot {
            background: var(--peach);
            border-color: #FFFFFF;
            animation: pulseDot 1.4s ease-in-out infinite;
        }


        .ease-parcel-step-title {
            margin: 0;
            font-size: 12.5px;
            font-weight: 600;
            color: #FFFFFF;
        }


        .ease-parcel-step-time {
            display: block;
            margin-top: 3px;
            font-size: 10px;
            color: rgba(255,255,255,0.48);
        }


        .ease-parcel-step:not(.done):not(.active) .ease-parcel-step-title,
        .ease-parcel-step:not(.done):not(.active) .ease-parcel-step-time {
            color: rgba(255,255,255,0.38);
        }


        /* =========================================================
           REALISTIC MAROON FOOTER
           No whitespace gap above or below.
        ========================================================== */

        .landing-footer {
            width: 100%;
            margin: 0;
            padding: 30px 5.5% 14px;

            display: block;

            background:
                linear-gradient(145deg, #3D080C 0%, #52070B 52%, #650E14 100%);

            color: rgba(255,255,255,0.70);
            border-top: 1px solid rgba(255,255,255,0.07);
        }


        .footer-inner {
            width: min(1360px, 100%);
            margin: 0 auto;
        }


        .footer-main {
            display: grid;
            grid-template-columns: 1.45fr repeat(3, 1fr);
            gap: 32px;
            padding-bottom: 26px;
            align-items: start;
        }


        .footer-brand {
            display: block;
            min-width: 0;
        }


        .footer-brand img {
            width: 505px;
            height: auto;
            display: block;
            object-fit: contain;
            margin-top: -160px;
            margin-bottom: -142px;
            margin-left: -88px;
            transform: translateY(-0px);
        }


        .footer-brand-tagline {
            max-width: 290px;
            margin: 0;
            font-size: 12px;
            line-height: 1.75;
            color: rgba(255,255,255,0.62);
        }


        .footer-trust-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }


        .footer-trust-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 10px;
            border: 1px solid rgba(255,255,255,0.13);
            border-radius: 999px;
            background: rgba(255,255,255,0.045);
            color: rgba(255,255,255,0.68);
            font-size: 9px;
            font-weight: 600;
        }


        .footer-trust-chip svg {
            width: 12px;
            height: 12px;
            color: var(--peach);
            flex-shrink: 0;
        }


        .footer-column {
            min-width: 0;
        }


        .footer-column > div[style*="margin-top:18px"] {
            margin-top: 12px !important;
        }


        .footer-column-title {
            margin: 0 0 12px;
            font-size: 12px;
            font-weight: 700;
            color: #FFFFFF;
            letter-spacing: 0.15px;
        }


        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }


        .footer-link {
            width: fit-content;
            font-size: 11px;
            color: rgba(255,255,255,0.58);
            transition: color 0.2s ease, transform 0.2s ease;
        }


        .footer-link:hover {
            color: var(--peach);
            transform: translateX(2px);
        }


        .footer-contact-item {
            display: flex;
            gap: 9px;
            align-items: flex-start;
            margin-bottom: 8px;
        }


        .footer-contact-icon {
            width: 14px;
            height: 14px;
            flex: 0 0 14px;
            color: var(--peach);
            margin-top: 2px;
        }


        .footer-contact-text {
            font-size: 10.5px;
            line-height: 1.55;
            color: rgba(255,255,255,0.58);
        }


        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding-top: 12px;
            border-top: 1px solid rgba(255,255,255,0.10);
        }


        .footer-text {
            margin: 0;
            font-size: 9.5px;
            line-height: 1.6;
            color: rgba(255,255,255,0.48);
        }


        .footer-legal {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }


        .footer-legal a {
            font-size: 9px;
            color: rgba(255,255,255,0.45);
            transition: color 0.2s ease;
        }


        .footer-legal a:hover {
            color: #FFFFFF;
        }


        .footer-socials {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-top: 12px;
        }


        .footer-social {
            width: 31px;
            height: 31px;
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            background: rgba(255,255,255,0.025);
            transition: all 0.25s ease;
        }


        .footer-social:hover {
            background: var(--peach);
            border-color: var(--peach);
            color: var(--maroon-dark);
            transform: translateY(-2px);
        }


        .footer-social svg {
            width: 13px;
            height: 13px;
        }


        /* =========================================================
           AUTH MODAL
           ONLY LOGIN + SIGNUP USE THIS
        ========================================================== */

        .login-modal {

            position:
                fixed;

            inset:
                0;

            z-index:
                9999;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                20px;

            opacity:
                0;

            visibility:
                hidden;

            pointer-events:
                none;

            transition:
                opacity 0.32s
                cubic-bezier(
                    0.22,
                    1,
                    0.36,
                    1
                ),
                padding 0.34s
                cubic-bezier(
                    0.22,
                    1,
                    0.36,
                    1
                );
        }


        .login-modal.show {

            opacity:
                1;

            visibility:
                visible;

            pointer-events:
                auto;
        }


        .login-modal-overlay {

            position:
                absolute;

            inset:
                0;

            background:
                rgba(
                    20,
                    5,
                    6,
                    0.42
                );

            backdrop-filter:
                blur(7px);

            -webkit-backdrop-filter:
                blur(7px);

            opacity:
                0;

            transition:
                opacity 0.32s
                cubic-bezier(
                    0.22,
                    1,
                    0.36,
                    1
                );
        }


        .login-modal.show
        .login-modal-overlay {

            opacity:
                1;
        }


        .login-modal-box {

            position:
                relative;

            z-index:
                2;

            width:
                min(
                    100%,
                    960px
                );

            height:
                min(
                    92vh,
                    680px
                );

            background:
                transparent;

            border-radius:
                22px;

            overflow:
                hidden;

            box-shadow:
                0 30px 80px
                rgba(
                    0,
                    0,
                    0,
                    0.30
                );

            transform:
                scale(
                    0.965
                )
                translateY(
                    12px
                );

            opacity:
                0;

            transition:
                transform 0.38s
                cubic-bezier(
                    0.22,
                    1,
                    0.36,
                    1
                ),
                opacity 0.24s
                ease,
                width 0.42s
                cubic-bezier(
                    0.22,
                    1,
                    0.36,
                    1
                ),
                height 0.42s
                cubic-bezier(
                    0.22,
                    1,
                    0.36,
                    1
                ),
                border-radius 0.42s
                cubic-bezier(
                    0.22,
                    1,
                    0.36,
                    1
                ),
                box-shadow 0.30s
                ease;

            will-change:
                transform,
                opacity,
                width,
                height;
        }


        .login-modal.show
        .login-modal-box {

            transform:
                scale(
                    1
                )
                translateY(
                    0
                );

            opacity:
                1;
        }


        /*
         * FULL-SCREEN HANDOFF
         *
         * This runs only when the iframe leaves the login/register
         * pages. Instead of abruptly switching from the modal to the
         * next Laravel page, the modal expands to the viewport first.
         */

        .login-modal.is-leaving {

            padding:
                0;

            opacity:
                1;

            visibility:
                visible;

            pointer-events:
                none;
        }


        .login-modal.is-leaving
        .login-modal-overlay {

            opacity:
                0;
        }


        .login-modal.is-leaving
        .login-modal-box {

            width:
                100vw;

            height:
                100vh;

            max-width:
                none;

            max-height:
                none;

            border-radius:
                0;

            box-shadow:
                none;

            transform:
                scale(
                    1
                )
                translateY(
                    0
                );
        }


        .login-modal-iframe {

            width:
                100%;

            height:
                100%;

            border:
                none;

            display:
                block;

            background:
                transparent;

            opacity:
                1;

            transition:
                opacity 0.2s ease;
        }


        .login-modal.is-leaving
        .login-modal-iframe {

            opacity:
                1;
        }


        .login-modal-close {

            position:
                absolute;

            top:
                12px;

            right:
                14px;

            z-index:
                20;

            width:
                36px;

            height:
                36px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border:
                none;

            border-radius:
                50%;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.95
                );

            color:
                var(--maroon-dark);

            font-size:
                25px;

            line-height:
                1;

            cursor:
                pointer;

            box-shadow:
                0 8px 20px
                rgba(
                    0,
                    0,
                    0,
                    0.12
                );

            transition:
                all 0.2s ease;
        }


        .login-modal-close:hover {

            background:
                var(--peach);

            transform:
                rotate(90deg);
        }


        .login-modal.is-leaving
        .login-modal-close {

            opacity:
                0;

            pointer-events:
                none;
        }


        /* =========================================================
           LARGE LAPTOP
        ========================================================== */

        @media (max-width: 1350px) {

            .landing-navbar {

                width:
                    calc(
                        100% - 100px
                    );
            }


            .hero {

                padding-left:
                    6%;

                padding-right:
                    6%;
            }


            .visual-card {

                width:
                    520px;

                height:
                    330px;
            }


            .guest-products {

                grid-template-columns:
                    repeat(
                        3,
                        minmax(
                            0,
                            1fr
                        )
                    );
            }

        }


        /* =========================================================
           TABLET
        ========================================================== */

        @media (max-width: 1050px) {

            .landing-navbar {

                width:
                    calc(
                        100% - 40px
                    );
            }


            .hero {

                grid-template-columns:
                    1fr;

                min-height:
                    auto;

                padding:
                    125px
                    6%
                    80px;

                text-align:
                    center;
            }


            .hero-copy {

                margin:
                    35px auto 0;

                max-width:
                    700px;

                transform:
                    none;
            }


            .hero-description {

                margin-left:
                    auto;

                margin-right:
                    auto;
            }


            .hero-actions {

                justify-content:
                    center;
            }


            .hero-visual {

                min-height:
                    500px;

                margin-top:
                    10px;

                transform:
                    none;
            }


            .visual-card {

                width:
                    520px;

                max-width:
                    100%;
            }


            .guest-shop-section {

                padding:
                    70px
                    6%
                    80px;
            }


            .guest-products {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(
                            0,
                            1fr
                        )
                    );

                max-width:
                    750px;
            }


            .login-modal-box {

                height:
                    min(
                        90vh,
                        650px
                    );

                width:
                    min(
                        92%,
                        900px
                    );
            }


            .ease-section {

                padding: 80px 6% 28px;
            }

            .ease-grid {

                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .ease-tracking {

                grid-template-columns: 1fr;
                padding: 32px;
                gap: 34px;
            }

        }


        /* =========================================================
           MOBILE
        ========================================================== */

        @media (max-width: 760px) {

            .landing-navbar {

                top:
                    10px;

                width:
                    calc(
                        100% - 24px
                    );

                height:
                    57px;

                padding:
                    0 14px;

                border-radius:
                    18px;
            }


            .landing-logo-bag {

                width:
                    27px;

                height:
                    27px;

                flex-basis:
                    27px;

                margin-right:
                    -1px;
            }


            .landing-logo-wordmark {

                width:
                    92px;
            }


            .login-button {

                min-width:
                    63px;

                height:
                    29px;

                padding:
                    0 11px;

                font-size:
                    9px;
            }


            .signup-button {

                min-width:
                    78px;

                height:
                    29px;

                padding:
                    0 11px;

                font-size:
                    9px;
            }


            .landing-nav-actions {

                gap:
                    6px;
            }


            /* HERO */

            .hero {

                min-height:
                    auto;

                padding:
                    105px
                    18px
                    55px;
            }


            .hero-copy {

                margin-top:
                    20px;
            }


            .hero-title {

                font-size:
                    45px;

                letter-spacing:
                    -1.5px;
            }


            .hero-description {

                max-width:
                    340px;

                font-size:
                    12px;
            }


            .hero-actions {

                flex-direction:
                    column;

                margin-top:
                    22px;
            }


            .hero-primary {

                width:
                    100%;

                max-width:
                    280px;
            }


            /* HERO VISUAL */

            .hero-visual {

                min-height:
                    400px;

                margin-top:
                    20px;
            }


            .visual-card {

                width:
                    min(
                        100%,
                        390px
                    );

                height:
                    260px;

                border-radius:
                    25px;
            }


            .visual-bag {

                width:
                    100px;

                top:
                    38px;
            }


            .visual-brand {

                top:
                    163px;

                width:
                    115px;
            }


            .visual-circle.one {

                width:
                    145px;

                height:
                    145px;

                right:
                    -30px;

                top:
                    -30px;
            }


            .visual-circle.two {

                width:
                    120px;

                height:
                    120px;

                left:
                    -40px;

                bottom:
                    -35px;
            }


            .floating-product.left {

                left:
                    -5px;

                top:
                    -20px;

                width:
                    128px;

                min-height:
                    118px;

                padding:
                    9px;
            }


            .floating-product.watch {

                right:
                    -5px;

                top:
                    -25px;

                width:
                    118px;

                min-height:
                    118px;

                padding:
                    9px;
            }


            .floating-product.shoes {

                right:
                    -5px;

                bottom:
                    -14px;

                width:
                    125px;

                min-height:
                    118px;

                padding:
                    9px;
            }


            .product-photo {

                height:
                    56px;
            }


            .product-photo svg {

                width:
                    41px;

                height:
                    41px;
            }


            .product-name {

                font-size:
                    8px;
            }


            .product-price {

                font-size:
                    8px;
            }


            .product-stars {

                font-size:
                    7px;
            }


            .visual-stat {

                left:
                    -5px;

                bottom:
                    -16px;

                width:
                    145px;

                padding:
                    10px;
            }


            .visual-stat-value {

                font-size:
                    16px;
            }


            .visual-stat-label {

                font-size:
                    7px;
            }


            .floating-circle.heart-one {

                right:
                    72px;

                top:
                    41px;

                width:
                    37px;

                height:
                    37px;
            }


            .floating-circle.heart-two,
            .floating-circle.cart {

                display:
                    none;
            }


            /* CATEGORY */

            .category-strip {

                min-height:
                    55px;
            }


            .category-track {

                gap:
                    25px;

                padding:
                    0 22px;

                animation-duration:
                    35s;
            }


            .category-item {

                font-size:
                    9px;

                gap:
                    7px;
            }


            .category-icon {

                width:
                    18px;

                height:
                    18px;
            }


            .category-icon svg {

                width:
                    18px;

                height:
                    18px;
            }


            .category-divider {

                font-size:
                    8px;
            }


            /* GUEST SHOP */

            .guest-shop-section {

                padding:
                    55px
                    18px
                    65px;
            }


            .guest-shop-heading {

                margin-bottom:
                    28px;
            }


            .guest-shop-label {

                font-size:
                    9px;
            }


            .guest-shop-heading h2 {

                font-size:
                    28px;
            }


            .guest-shop-heading p {

                font-size:
                    11px;
            }


            .guest-search-wrapper {

                margin-bottom:
                    30px;
            }


            .guest-search {

                height:
                    52px;

                padding:
                    0 50px
                    0 18px;

                font-size:
                    11px;
            }


            .guest-search-icon {

                right:
                    18px;

                width:
                    17px;

                height:
                    17px;
            }


            .guest-products {

                grid-template-columns:
                    1fr;

                max-width:
                    380px;

                gap:
                    18px;
            }


            .guest-product-image {

                height:
                    190px;
            }


            .guest-product-content {

                padding:
                    16px;
            }


            .guest-product-name {

                font-size:
                    13px;
            }


            .guest-product-price {

                font-size:
                    15px;
            }


            /* FOOTER */

            .landing-footer {
                padding: 36px 18px 14px;
            }


            .footer-main {
                grid-template-columns: 1fr 1fr;
                gap: 24px 18px;
                padding-bottom: 22px;
            }


            .footer-brand {
                grid-column: 1 / -1;
            }


            .footer-brand img {
                width: 115px;
                margin-top: 0;
                margin-bottom: 15px;
                transform: none;
            }


            .footer-brand-tagline {
                max-width: 430px;
                font-size: 11px;
            }


            .footer-column-title {
                margin-bottom: 13px;
                font-size: 11px;
            }


            .footer-link {
                font-size: 10px;
            }


            .footer-contact-text {
                font-size: 9.5px;
            }


            .footer-bottom {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }


            /* AUTH MODAL */

            .login-modal {

                padding:
                    10px;
            }


            .login-modal-box {

                width:
                    100%;

                height:
                    94vh;

                border-radius:
                    18px;
            }


            .login-modal-close {

                width:
                    32px;

                height:
                    32px;

                top:
                    8px;

                right:
                    8px;

                font-size:
                    22px;
            }


            /* EASE SECTION */

            .ease-section {

                padding: 64px 18px 24px;
            }

            .ease-heading h2 {

                font-size: 26px;
            }

            .ease-grid {

                grid-template-columns: 1fr;
                gap: 16px;
                margin-bottom: 40px;
            }

            .ease-tracking {

                padding: 24px;
                gap: 30px;
            }

            .ease-tracking-copy h3 {

                font-size: 20px;
            }

            .ease-stats-row {

                gap: 18px;
            }

        }


        /* =========================================================
           REDUCED MOTION
        ========================================================== */

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {

                animation-duration:
                    0.001ms !important;

                animation-iteration-count:
                    1 !important;

                transition-duration:
                    0.001ms !important;

                scroll-behavior:
                    auto !important;
            }

        }


        /* =========================================================
           GLOBAL SCROLL REVEAL
           Product section → Why ShopEase
        ========================================================== */

        .scroll-reveal {
            opacity: 0 !important;
            transform: translate3d(0, 42px, 0) !important;
            transition:
                opacity 0.85s cubic-bezier(0.22, 1, 0.36, 1),
                transform 0.85s cubic-bezier(0.22, 1, 0.36, 1) !important;
            will-change: opacity, transform;
        }

        .scroll-reveal.is-visible {
            opacity: 1 !important;
            transform: translate3d(0, 0, 0) !important;
        }

        #guestShop .guest-product-card.scroll-reveal {
            transition-duration: 0.75s !important;
        }

        .landing-footer {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }


        #guestShop .guest-shop-heading.scroll-reveal {
            transform: translate3d(0, 30px, 0) !important;
        }

        #guestShop .guest-search-wrapper.scroll-reveal {
            transform: translate3d(0, 25px, 0) !important;
        }

        #whyShopease .scroll-reveal {
            transition:
                opacity 0.78s cubic-bezier(0.22, 1, 0.36, 1),
                transform 0.78s cubic-bezier(0.22, 1, 0.36, 1) !important;
        }


        @media (prefers-reduced-motion: reduce) {
            .scroll-reveal,
            .scroll-reveal.is-visible {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }

    </style>

</head>


<body>

<div class="landing-page">


    <!-- =========================================================
         NAVBAR
    ========================================================== -->

    <header class="landing-navbar">


        <!-- LOGO -->

        <a
            href="{{ url('/') }}"
            class="landing-logo"
            aria-label="ShopEase Home"
        >



            <img
                src="{{ asset('icons/logos/shop-ease.png') }}"
                class="landing-logo-wordmark"
                alt="ShopEase"
            >

        </a>



        <!-- ACTIONS -->

        <div class="landing-nav-actions">


            <!-- LOGIN -->

            <button
                type="button"
                class="login-button"
                id="openLoginModal"
            >

                Log In

            </button>


            <!-- SIGN UP -->

            <button
                type="button"
                class="signup-button"
                id="openSignupModal"
            >

                Sign Up

            </button>

        </div>

    </header>



    <!-- =========================================================
         HERO
    ========================================================== -->

    <section
        class="hero"
        id="home"
    >


        <div class="hero-bg-circle one"></div>

        <div class="hero-bg-circle two"></div>

        <div class="hero-bg-circle three"></div>



        <!-- HERO COPY -->

        <div class="hero-copy">


            <h1 class="hero-title">

                Shop Easier,<br>

                <span>
                    Live Better.
                </span>

            </h1>


            <p class="hero-description">

                Discover products you love, enjoy a smoother
                shopping experience, and have everything you need
                just a few clicks away.

            </p>


            <div class="hero-actions">


                <!-- =================================================
                     START SHOPPING
                ================================================== -->

                <a
                    href="#guestShop"
                    class="hero-primary"
                    id="startShoppingButton"
                >

                    Start Shopping


                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path d="M5 12h14" />

                        <path d="M13 6l6 6-6 6" />

                    </svg>

                </a>

            </div>

        </div>



        <!-- HERO VISUAL -->

        <div class="hero-visual">


            <div
                class="visual-card"
                id="visualCard"
            >


                <div class="visual-circle one"></div>

                <div class="visual-circle two"></div>



                <!-- BAG -->

                <div class="visual-bag">

                    <img
                        src="{{ asset('icons/logos/bag.png') }}"
                        alt="ShopEase Bag"
                    >

                </div>



                <!-- BRAND -->

                <div class="visual-brand">

                    <img
                        src="{{ asset('icons/logos/ShopEase.png') }}"
                        alt="ShopEase"
                    >

                </div>



                <!-- HEADPHONES -->

                <div class="floating-product left">


                    <div class="product-photo">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#52070B"
                            stroke-width="1.3"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <path d="M6 8h12" />

                            <path
                                d="M8 8
                                   V6
                                   a4 4 0 0 1 8 0
                                   v2"
                            />

                            <rect
                                x="5"
                                y="8"
                                width="14"
                                height="11"
                                rx="2"
                            />

                        </svg>

                    </div>


                    <p class="product-name">
                        Wireless Headphones
                    </p>


                    <p class="product-price">
                        ₱1,299
                    </p>


                    <div class="product-stars">
                        ★★★★★
                    </div>

                </div>



                <!-- WATCH -->

                <div class="floating-product watch">


                    <div class="product-photo">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#52070B"
                            stroke-width="1.4"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <rect
                                x="7"
                                y="5"
                                width="10"
                                height="14"
                                rx="3"
                            />

                            <path d="M9 2h6" />

                            <path d="M9 22h6" />

                            <path d="M10 9h4" />

                            <path d="M10 12h3" />

                        </svg>

                    </div>


                    <p class="product-name">
                        Smart Watch
                    </p>


                    <p class="product-price">
                        ₱2,499
                    </p>


                    <div class="product-stars">
                        ★★★★★
                    </div>

                </div>



                <!-- SHOES -->

                <div class="floating-product shoes">


                    <div class="product-photo">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#52070B"
                            stroke-width="1.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <path
                                d="M4 16
                                   c3 1 6 1 9-1
                                   l3 3
                                   h4
                                   v2
                                   H4
                                   z"
                            />

                            <path d="M8 10l4 5" />

                            <path d="M11 9l4 4" />

                        </svg>

                    </div>


                    <p class="product-name">
                        Running Shoes
                    </p>


                    <p class="product-price">
                        ₱1,899
                    </p>


                    <div class="product-stars">
                        ★★★★★
                    </div>

                </div>



                <!-- HAPPY SHOPPERS -->

                <div class="visual-stat">


                    <div class="visual-stat-top">

                        <div>

                            <span class="visual-stat-label">
                                Happy shoppers
                            </span>


                            <div class="visual-stat-value">
                                10,000+
                            </div>

                        </div>


                        <span class="visual-stat-change">
                            +24%
                        </span>

                    </div>


                    <div class="visual-stat-line">

                        <div class="visual-stat-progress"></div>

                    </div>

                </div>



                <!-- HEART -->

                <div class="floating-circle heart-one">

                    <svg
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >

                        <path
                            d="M12 21s-7-4.7-9.5-9
                               C.5 7.7 3 5 6.3 5
                               c1.8 0 3.5.9 4.7 2.3
                               C12.5 5.9 14.2 5 16 5
                               c3.3 0 5.8 2.7 3.8 7
                               C19 16.3 12 21 12 21z"
                        />

                    </svg>

                </div>



                <!-- SECOND HEART -->

                <div class="floating-circle heart-two">

                    <svg
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >

                        <path
                            d="M12 21s-7-4.7-9.5-9
                               C.5 7.7 3 5 6.3 5
                               c1.8 0 3.5.9 4.7 2.3
                               C12.5 5.9 14.2 5 16 5
                               c3.3 0 5.8 2.7 3.8 7
                               C19 16.3 12 21 12 21z"
                        />

                    </svg>

                </div>



                <!-- CART -->

                <div class="floating-circle cart">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path
                            d="M3 4h2l2.2 11h10.9l2-8H7"
                        />

                        <circle
                            cx="10"
                            cy="19"
                            r="1.5"
                        />

                        <circle
                            cx="17"
                            cy="19"
                            r="1.5"
                        />

                    </svg>

                </div>

            </div>

        </div>

    </section>



    <!-- =========================================================
         CATEGORY STRIP
    ========================================================== -->

    <section class="category-strip">

        <div class="category-track">


            <!-- PET SUPPLIES -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path d="M6 7h12l1 13H5L6 7z" />

                        <path d="M9 7a3 3 0 0 1 6 0" />

                    </svg>

                </span>

                Pet Supplies

            </div>


            <span class="category-divider">✦</span>



            <!-- ELECTRONICS -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <rect
                            x="5"
                            y="3"
                            width="14"
                            height="18"
                            rx="2"
                        />

                        <path d="M9 7h6" />

                        <path d="M9 11h6" />

                        <path d="M9 15h3" />

                    </svg>

                </span>

                Electronics and Gadgets

            </div>


            <span class="category-divider">✦</span>



            <!-- WOMEN -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path
                            d="M7 4l5-2 5 2 3 5-4 2v9H8v-9L4 9l3-5z"
                        />

                        <path d="M9 12h6" />

                    </svg>

                </span>

                Women's Apparel

            </div>


            <span class="category-divider">✦</span>



            <!-- MEN -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path
                            d="M7 5l5-3 5 3 3 6-4 2v8H8v-8l-4-2 3-6z"
                        />

                        <path d="M9 13h6" />

                    </svg>

                </span>

                Men's Apparel

            </div>


            <span class="category-divider">✦</span>



            <!-- KIDS -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <circle
                            cx="9"
                            cy="8"
                            r="3"
                        />

                        <circle
                            cx="16"
                            cy="10"
                            r="2.5"
                        />

                        <path
                            d="M3 20c0-3.2 2.7-5.5 6-5.5s6 2.3 6 5.5"
                        />

                        <path
                            d="M14 15c2.8 0 5 1.8 5 5"
                        />

                    </svg>

                </span>

                Kids and Baby

            </div>


            <span class="category-divider">✦</span>



            <!-- HOME -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path d="M3 11l9-8 9 8" />

                        <path d="M5 10v10h14V10" />

                        <path d="M9 20v-6h6v6" />

                    </svg>

                </span>

                Home and Garden

            </div>


            <span class="category-divider">✦</span>



            <!-- SPORTS -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                        />

                        <path d="M12 7v5l3 2" />

                    </svg>

                </span>

                Sports and Outdoors

            </div>


            <span class="category-divider">✦</span>



            <!-- HEALTH -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path
                            d="M12 3l8 4v5c0 4.5-3 7.5-8 9-5-1.5-8-4.5-8-9V7l8-4z"
                        />

                        <path d="M9 12l2 2 4-5" />

                    </svg>

                </span>

                Health and Beauty

            </div>


            <span class="category-divider">✦</span>



            <!-- BOOKS -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path d="M5 4h14v16H5z" />

                        <path d="M8 4v16" />

                        <path d="M10 8h6" />

                        <path d="M10 12h6" />

                    </svg>

                </span>

                Books and Media

            </div>


            <span class="category-divider">✦</span>



            <!-- FOOD -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path d="M4 7h16" />

                        <path d="M5 7v10h14V7" />

                        <path d="M8 4h8" />

                        <path d="M8 11h8" />

                    </svg>

                </span>

                Food and Gourmet

            </div>


            <span class="category-divider">✦</span>



            <!-- AUTOMOTIVE -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path d="M3 13l2-5h10l3 5" />

                        <rect
                            x="3"
                            y="13"
                            width="18"
                            height="5"
                            rx="2"
                        />

                        <circle
                            cx="7"
                            cy="18.5"
                            r="1.5"
                        />

                        <circle
                            cx="17"
                            cy="18.5"
                            r="1.5"
                        />

                    </svg>

                </span>

                Automotive & Motorcycle

            </div>


            <span class="category-divider">✦</span>



            <!-- FURNITURE -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <rect
                            x="3"
                            y="5"
                            width="18"
                            height="14"
                            rx="2"
                        />

                        <path d="M8 5v14" />

                        <path d="M8 9h13" />

                    </svg>

                </span>

                Furniture and Office Equipment

            </div>


            <span class="category-divider">✦</span>



            <!-- JEWELRY -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <circle
                            cx="8"
                            cy="12"
                            r="3"
                        />

                        <path d="M11 12h8" />

                        <path d="M16 9v6" />

                    </svg>

                </span>

                Jewelry and Watches

            </div>


            <span class="category-divider">✦</span>



            <!-- SCHOOL -->

            <div class="category-item">

                <span class="category-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path d="M5 4h14v16H5z" />

                        <path d="M8 8h8" />

                        <path d="M8 12h5" />

                        <path d="M8 16h8" />

                    </svg>

                </span>

                Office and School Supplies

            </div>



            <!-- =====================================================
                 DUPLICATE SET
            ====================================================== -->

            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                        stroke-linecap="round"
                    >
                        <path d="M6 7h12l1 13H5L6 7z" />
                        <path d="M9 7a3 3 0 0 1 6 0" />
                    </svg>
                </span>

                Pet Supplies

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <rect
                            x="5"
                            y="3"
                            width="14"
                            height="18"
                            rx="2"
                        />
                    </svg>
                </span>

                Electronics and Gadgets

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path d="M7 4l5-2 5 2 3 5-4 2v9H8v-9L4 9l3-5z" />
                    </svg>
                </span>

                Women's Apparel

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path d="M7 5l5-3 5 3 3 6-4 2v8H8v-8l-4-2 3-6z" />
                    </svg>
                </span>

                Men's Apparel

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <circle cx="9" cy="8" r="3" />
                        <circle cx="16" cy="10" r="2.5" />
                    </svg>
                </span>

                Kids and Baby

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path d="M3 11l9-8 9 8" />
                        <path d="M5 10v10h14V10" />
                    </svg>
                </span>

                Home and Garden

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <circle cx="12" cy="12" r="9" />
                    </svg>
                </span>

                Sports and Outdoors

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path d="M12 3l8 4v5c0 4.5-3 7.5-8 9-5-1.5-8-4.5-8-9V7l8-4z" />
                    </svg>
                </span>

                Health and Beauty

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path d="M5 4h14v16H5z" />
                        <path d="M8 4v16" />
                    </svg>
                </span>

                Books and Media

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path d="M4 7h16" />
                        <path d="M5 7v10h14V7" />
                    </svg>
                </span>

                Food and Gourmet

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path d="M3 13l2-5h10l3 5" />
                        <rect x="3" y="13" width="18" height="5" rx="2" />
                    </svg>
                </span>

                Automotive & Motorcycle

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <rect x="3" y="5" width="18" height="14" rx="2" />
                        <path d="M8 5v14" />
                    </svg>
                </span>

                Furniture and Office Equipment

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <circle cx="8" cy="12" r="3" />
                        <path d="M11 12h8" />
                    </svg>
                </span>

                Jewelry and Watches

            </div>


            <span class="category-divider">✦</span>


            <div class="category-item">

                <span class="category-icon">
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path d="M5 4h14v16H5z" />
                        <path d="M8 8h8" />
                        <path d="M8 12h5" />
                    </svg>
                </span>

                Office and School Supplies

            </div>

        </div>

    </section>



    <!-- =========================================================
         GUEST SHOP
    ========================================================== -->

    <section
        class="guest-shop-section"
        id="guestShop"
    >


        <div class="guest-shop-heading">

        


            <h2>
                Find something you'll love.
            </h2>


            <p>
                Browse our products and discover something
                that fits your everyday needs.
            </p>

        </div>



        <!-- SEARCH -->

        <div class="guest-search-wrapper">

            <input
                type="text"
                id="guestSearch"
                class="guest-search"
                placeholder="Search for products..."
                autocomplete="off"
            >


            <svg
                class="guest-search-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
            >

                <circle
                    cx="11"
                    cy="11"
                    r="7"
                />

                <path
                    d="M16 16l5 5"
                />

            </svg>

        </div>



        <!-- PRODUCTS -->

        <div
            class="guest-products"
            id="guestProducts"
        >


            <!-- PRODUCT 1 -->

            <article
                class="guest-product-card"
                data-name="Wireless Headphones"
                data-category="Electronics and Gadgets"
            >

                <div class="guest-product-image">

                    <span class="guest-product-badge">
                        Popular
                    </span>


                    <img
                        src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=85"
                        alt="Wireless Headphones"
                        loading="lazy"
                        decoding="async"
                    >
</div>


                <div class="guest-product-content">

                    <p class="guest-product-category">
                        Electronics
                    </p>


                    <h3 class="guest-product-name">
                        Wireless Headphones
                    </h3>


                    <div class="guest-product-rating">

                        <span class="guest-product-stars">
                            ★★★★★
                        </span>

                        <span class="guest-product-rating-number">
                            4.9
                        </span>

                    </div>


                    <div class="guest-product-bottom">

                        <span class="guest-product-price">
                            ₱1,299
                        </span>


                        <button
                            type="button"
                            class="guest-product-button"
                        >
                            View Product
                        </button>

                    </div>

                </div>

            </article>



            <!-- PRODUCT 2 -->

            <article
                class="guest-product-card"
                data-name="Smart Watch"
                data-category="Jewelry and Watches"
            >

                <div class="guest-product-image">

                    <span class="guest-product-badge">
                        New
                    </span>


                    <img
                        src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=900&q=85"
                        alt="Smart Watch"
                        loading="lazy"
                        decoding="async"
                    >
</div>


                <div class="guest-product-content">

                    <p class="guest-product-category">
                        Jewelry & Watches
                    </p>


                    <h3 class="guest-product-name">
                        Smart Watch
                    </h3>


                    <div class="guest-product-rating">

                        <span class="guest-product-stars">
                            ★★★★★
                        </span>

                        <span class="guest-product-rating-number">
                            4.8
                        </span>

                    </div>


                    <div class="guest-product-bottom">

                        <span class="guest-product-price">
                            ₱2,499
                        </span>


                        <button
                            type="button"
                            class="guest-product-button"
                        >
                            View Product
                        </button>

                    </div>

                </div>

            </article>



            <!-- PRODUCT 3 -->

            <article
                class="guest-product-card"
                data-name="Running Shoes"
                data-category="Sports and Outdoors"
            >

                <div class="guest-product-image">
                    <img
                        src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=85"
                        alt="Running Shoes"
                        loading="lazy"
                        decoding="async"
                    >
</div>


                <div class="guest-product-content">

                    <p class="guest-product-category">
                        Sports and Outdoors
                    </p>


                    <h3 class="guest-product-name">
                        Running Shoes
                    </h3>


                    <div class="guest-product-rating">

                        <span class="guest-product-stars">
                            ★★★★★
                        </span>

                        <span class="guest-product-rating-number">
                            4.7
                        </span>

                    </div>


                    <div class="guest-product-bottom">

                        <span class="guest-product-price">
                            ₱1,899
                        </span>


                        <button
                            type="button"
                            class="guest-product-button"
                        >
                            View Product
                        </button>

                    </div>

                </div>

            </article>



            <!-- PRODUCT 4 -->

            <article
                class="guest-product-card"
                data-name="Classic Tote Bag"
                data-category="Women's Apparel"
            >

                <div class="guest-product-image">

                    <span class="guest-product-badge">
                        Sale
                    </span>


                    <img
                        src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=900&q=85"
                        alt="Classic Tote Bag"
                        loading="lazy"
                        decoding="async"
                    >
</div>


                <div class="guest-product-content">

                    <p class="guest-product-category">
                        Women's Apparel
                    </p>


                    <h3 class="guest-product-name">
                        Classic Tote Bag
                    </h3>


                    <div class="guest-product-rating">

                        <span class="guest-product-stars">
                            ★★★★★
                        </span>

                        <span class="guest-product-rating-number">
                            4.8
                        </span>

                    </div>


                    <div class="guest-product-bottom">

                        <span class="guest-product-price">
                            ₱899
                        </span>


                        <button
                            type="button"
                            class="guest-product-button"
                        >
                            View Product
                        </button>

                    </div>

                </div>

            </article>



            <!-- PRODUCT 5 -->

            <article
                class="guest-product-card"
                data-name="Daily Skincare Set"
                data-category="Health and Beauty"
            >

                <div class="guest-product-image">
                    <img
                        src="https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=85"
                        alt="Daily Skincare Set"
                        loading="lazy"
                        decoding="async"
                    >
</div>


                <div class="guest-product-content">

                    <p class="guest-product-category">
                        Health and Beauty
                    </p>


                    <h3 class="guest-product-name">
                        Daily Skincare Set
                    </h3>


                    <div class="guest-product-rating">

                        <span class="guest-product-stars">
                            ★★★★★
                        </span>

                        <span class="guest-product-rating-number">
                            4.9
                        </span>

                    </div>


                    <div class="guest-product-bottom">

                        <span class="guest-product-price">
                            ₱1,599
                        </span>


                        <button
                            type="button"
                            class="guest-product-button"
                        >
                            View Product
                        </button>

                    </div>

                </div>

            </article>



            <!-- PRODUCT 6 -->

            <article
                class="guest-product-card"
                data-name="Home Aroma Diffuser"
                data-category="Home and Garden"
            >

                <div class="guest-product-image">
                    <img
                        src="https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&w=900&q=85"
                        alt="Home Aroma Diffuser"
                        loading="lazy"
                        decoding="async"
                    >
</div>


                <div class="guest-product-content">

                    <p class="guest-product-category">
                        Home and Garden
                    </p>


                    <h3 class="guest-product-name">
                        Home Aroma Diffuser
                    </h3>


                    <div class="guest-product-rating">

                        <span class="guest-product-stars">
                            ★★★★★
                        </span>

                        <span class="guest-product-rating-number">
                            4.6
                        </span>

                    </div>


                    <div class="guest-product-bottom">

                        <span class="guest-product-price">
                            ₱1,099
                        </span>


                        <button
                            type="button"
                            class="guest-product-button"
                        >
                            View Product
                        </button>

                    </div>

                </div>

            </article>



            <!-- PRODUCT 7 -->

            <article
                class="guest-product-card"
                data-name="Everyday Essentials Box"
                data-category="Office and School Supplies"
            >

                <div class="guest-product-image">

                    <span class="guest-product-badge">
                        Best Seller
                    </span>


                    <img
                        src="https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?auto=format&fit=crop&w=900&q=85"
                        alt="Everyday Essentials Box"
                        loading="lazy"
                        decoding="async"
                    >
</div>


                <div class="guest-product-content">

                    <p class="guest-product-category">
                        Office and School
                    </p>


                    <h3 class="guest-product-name">
                        Everyday Essentials Box
                    </h3>


                    <div class="guest-product-rating">

                        <span class="guest-product-stars">
                            ★★★★★
                        </span>

                        <span class="guest-product-rating-number">
                            4.9
                        </span>

                    </div>


                    <div class="guest-product-bottom">

                        <span class="guest-product-price">
                            ₱1,249
                        </span>


                        <button
                            type="button"
                            class="guest-product-button"
                        >
                            View Product
                        </button>

                    </div>

                </div>

            </article>



            <!-- PRODUCT 8 -->

            <article
                class="guest-product-card"
                data-name="Great Deal Gift Set"
                data-category="Food and Gourmet"
            >

                <div class="guest-product-image">

                    <span class="guest-product-badge">
                        Deal
                    </span>


                    <img
                        src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=900&q=85"
                        alt="Great Deal Gift Set"
                        loading="lazy"
                        decoding="async"
                    >
</div>


                <div class="guest-product-content">

                    <p class="guest-product-category">
                        Food and Gourmet
                    </p>


                    <h3 class="guest-product-name">
                        Great Deal Gift Set
                    </h3>


                    <div class="guest-product-rating">

                        <span class="guest-product-stars">
                            ★★★★★
                        </span>

                        <span class="guest-product-rating-number">
                            4.7
                        </span>

                    </div>


                    <div class="guest-product-bottom">

                        <span class="guest-product-price">
                            ₱799
                        </span>


                        <button
                            type="button"
                            class="guest-product-button"
                        >
                            View Product
                        </button>

                    </div>

                </div>

            </article>

        </div>



        <!-- NO RESULTS -->

        <div
            class="guest-no-results"
            id="guestNoResults"
        >

            <strong>
                No products found.
            </strong>

            Try searching for another product.

        </div>

    </section>



    <!-- =========================================================
         WHY SHOP WITH SHOPEASE / LIVE TRACKING
    ========================================================== -->

    <section class="ease-section" id="whyShopease">

        <div class="ease-blob a"></div>
        <div class="ease-blob b"></div>

        <div class="ease-inner">

            <div class="ease-heading" id="easeHeading">

                <span class="ease-label">
                    Why ShopEase
                </span>

                <h2>
                    Everything about your order,
                    made effortless.
                </h2>

                <p>
                    From checkout to your doorstep, we built
                    ShopEase around one idea: shopping should
                    feel light, fast, and worry-free.
                </p>

            </div>


            <!-- FEATURE CARDS -->

            <div class="ease-grid" id="easeGrid">


                <!-- REAL-TIME TRACKING -->

                <div class="ease-card">

                    <div class="ease-card-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M3 12a9 9 0 1 0 9-9" />
                            <path d="M12 7v5l3.5 2" />
                            <path d="M3 4v5h5" />
                        </svg>

                    </div>

                    <h3>Real-Time Parcel Tracking</h3>

                    <p>
                        Watch your order move live, from the
                        seller's shelf to your front door, updated
                        the moment it changes.
                    </p>

                </div>


                <!-- SECURE CHECKOUT -->

                <div class="ease-card">

                    <div class="ease-card-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <rect x="5" y="11" width="14" height="9" rx="2" />
                            <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                        </svg>

                    </div>

                    <h3>Secure Checkout</h3>

                    <p>
                        Every payment is encrypted end-to-end,
                        so you can check out knowing your details
                        stay protected.
                    </p>

                </div>


                <!-- FAST DELIVERY -->

                <div class="ease-card">

                    <div class="ease-card-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M3 16V7a1 1 0 0 1 1-1h9v10" />
                            <path d="M13 10h4l4 4v2" />
                            <circle cx="7.5" cy="17.5" r="1.7" />
                            <circle cx="17.5" cy="17.5" r="1.7" />
                        </svg>

                    </div>

                    <h3>Fast, Nationwide Delivery</h3>

                    <p>
                        Reliable courier partners bring your
                        orders to almost anywhere in the country,
                        without the long wait.
                    </p>

                </div>


                <!-- 24/7 SUPPORT -->

                <div class="ease-card">

                    <div class="ease-card-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M4 12a8 8 0 0 1 16 0v5a2 2 0 0 1-2 2h-1v-7h3" />
                            <path d="M4 17v-5h3v7H6a2 2 0 0 1-2-2z" />
                        </svg>

                    </div>

                    <h3>Customer Support</h3>

                    <p>
                        Got a question about your order? Our
                        support team is online any time, any
                        day, ready to help.
                    </p>

                </div>


                <!-- EASY RETURNS -->

                <div class="ease-card">

                    <div class="ease-card-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M4 9a8 8 0 1 1 1.5 7" />
                            <path d="M4 4v5h5" />
                        </svg>

                    </div>

                    <h3>Quick Action</h3>

                    <p>
                        Have problems? No worries. Our admin is ready to help you with any issues, and your problem can be resolved in a matter of days.
                    </p>

                </div>


                <!-- VERIFIED SELLERS -->

                <div class="ease-card">

                    <div class="ease-card-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 3l7 3.5v5c0 4.5-3 7.5-7 8.5-4-1-7-4-7-8.5v-5L12 3z" />
                            <path d="M9 12l2 2 4-4.5" />
                        </svg>

                    </div>

                    <h3>Verified Sellers Only</h3>

                    <p>
                        Every seller on ShopEase goes through
                        verification, so you always know who
                        you're buying from.
                    </p>

                </div>

            </div>



            <!-- LIVE TRACKING PANEL -->

            <div class="ease-tracking" id="easeTracking">

                <div class="ease-tracking-copy">

                    <span class="ease-label">
                        Track Your Parcel
                    </span>

                    <h3>
                        Know exactly where your
                        order is, in real time.
                    </h3>

                    <p>
                        No more guessing games. Follow your
                        parcel's journey step by step, from the
                        moment it's packed to the moment it
                        reaches your door.
                    </p>

                    <div class="ease-stats-row">

                        <div class="ease-stat">
                            <div class="ease-stat-value">98%</div>
                            <span class="ease-stat-label">On-time delivery</span>
                        </div>

                        <div class="ease-stat">
                            <div class="ease-stat-value">24/7</div>
                            <span class="ease-stat-label">Live tracking</span>
                        </div>

                       

                    </div>

                </div>


                <!-- PARCEL TRACKER MOCK -->

                <div class="ease-parcel-card">

                    <div class="ease-parcel-top">

                        <span class="ease-parcel-id">
                            Parcel #SE-48210
                        </span>

                        <span class="ease-parcel-live">
                            <span class="ease-parcel-live-dot"></span>
                            Live
                        </span>

                    </div>


                    <div class="ease-parcel-route">

                        <div class="ease-parcel-line"></div>
                        <div class="ease-parcel-progress-line"></div>


                        <div class="ease-parcel-step done">

                            <span class="ease-parcel-dot"></span>

                            <div>
                                <p class="ease-parcel-step-title">
                                    Order confirmed
                                </p>
                                <span class="ease-parcel-step-time">
                                    Today, 8:02 AM
                                </span>
                            </div>

                        </div>


                        <div class="ease-parcel-step done">

                            <span class="ease-parcel-dot"></span>

                            <div>
                                <p class="ease-parcel-step-title">
                                    Packed by seller
                                </p>
                                <span class="ease-parcel-step-time">
                                    Today, 9:41 AM
                                </span>
                            </div>

                        </div>


                        <div class="ease-parcel-step active">

                            <span class="ease-parcel-dot"></span>

                            <div>
                                <p class="ease-parcel-step-title">
                                    Out for delivery
                                </p>
                                <span class="ease-parcel-step-time">
                                    Today, 1:15 PM
                                </span>
                            </div>

                        </div>


                        <div class="ease-parcel-step">

                            <span class="ease-parcel-dot"></span>

                            <div>
                                <p class="ease-parcel-step-title">
                                    Delivered
                                </p>
                                <span class="ease-parcel-step-time">
                                    Estimated today, 5:00 PM
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- =========================================================
         REALISTIC FOOTER
    ========================================================== -->

    <footer class="landing-footer">

        <div class="footer-inner">

            <div class="footer-main">

                <div class="footer-brand">

                    <img
                        src="{{ asset('icons/logos/ShopEase.png') }}"
                        alt="ShopEase"
                    >

                    <p class="footer-brand-tagline">
                        Shop smarter, discover products you love,
                        and enjoy a smoother experience from checkout
                        to delivery.
                    </p>

                    <div class="footer-trust-row">

                        <span class="footer-trust-chip">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="10" width="16" height="10" rx="2" />
                                <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                            </svg>
                            Secure Checkout
                        </span>

                        <span class="footer-trust-chip">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7h11v10H3z" />
                                <path d="M14 10h4l3 3v4h-7z" />
                                <circle cx="7" cy="19" r="1.5" />
                                <circle cx="18" cy="19" r="1.5" />
                            </svg>
                            Order Tracking
                        </span>

                    </div>

                    <div class="footer-socials">

                        <a href="#" class="footer-social" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 8h3V5h-3c-2.8 0-4 1.8-4 4v2H7v3h3v5h3v-5h3l.5-3H13V9c0-.7.3-1 1-1z" />
                            </svg>
                        </a>

                        <a href="#" class="footer-social" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="4" y="4" width="16" height="16" rx="4" />
                                <circle cx="12" cy="12" r="3.5" />
                                <circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none" />
                            </svg>
                        </a>

                        <a href="#" class="footer-social" aria-label="TikTok">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15 4v9.2a4.8 4.8 0 1 1-4-4.7v2.8a2 2 0 1 0 1 1.9V4h3z" />
                            </svg>
                        </a>

                        <a href="#" class="footer-social" aria-label="YouTube">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21 8.2a2.6 2.6 0 0 0-1.8-1.8C17.6 6 12 6 12 6s-5.6 0-7.2.4A2.6 2.6 0 0 0 3 8.2 27 27 0 0 0 2.6 12 27 27 0 0 0 3 15.8a2.6 2.6 0 0 0 1.8 1.8C6.4 18 12 18 12 18s5.6 0 7.2-.4a2.6 2.6 0 0 0 1.8-1.8 27 27 0 0 0 .4-3.8 27 27 0 0 0-.4-3.8z" />
                                <path d="M10 9.5l5 2.5-5 2.5z" fill="#3D080C" />
                            </svg>
                        </a>

                    </div>

                </div>


                <div class="footer-column">

                    <h3 class="footer-column-title">
                        Shop
                    </h3>

                    <div class="footer-links">
                        <a href="#guestShop" class="footer-link">All Products</a>
                        <a href="#guestShop" class="footer-link">Electronics &amp; Gadgets</a>
                        <a href="#guestShop" class="footer-link">Women's Apparel</a>
                        <a href="#guestShop" class="footer-link">Home &amp; Garden</a>
                        <a href="#guestShop" class="footer-link">Health &amp; Beauty</a>
                        <a href="#guestShop" class="footer-link">Great Deals</a>
                    </div>

                </div>


                <div class="footer-column">

                    <h3 class="footer-column-title">
                        Help &amp; Support
                    </h3>

                    <div class="footer-links">
                        <a href="#" class="footer-link">Order Tracking</a>
                        <a href="#" class="footer-link">Shipping Information</a>
                        <a href="#" class="footer-link">Returns &amp; Refunds</a>
                        <a href="#" class="footer-link">Contact Support</a>
                        <a href="#" class="footer-link">Frequently Asked Questions</a>
                    </div>

                </div>


                <div class="footer-column">

                    <h3 class="footer-column-title">
                        Sell on ShopEase
                    </h3>

                    <div class="footer-links">
                        <a href="#" class="footer-link">Become a Seller</a>
                        <a href="#" class="footer-link">Seller Guidelines</a>
                        <a href="#" class="footer-link">Seller Compliance</a>
                        <a href="#" class="footer-link">Seller Support</a>
                    </div>

                    <div style="margin-top:18px;">

                        <div class="footer-contact-item">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 5h16v14H4z" />
                                <path d="m4 7 8 6 8-6" />
                            </svg>
                            <span class="footer-contact-text">
                                support@shopease.com
                            </span>
                        </div>

                        <div class="footer-contact-item">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6.5 3.5h3l1.5 4-2 1.6a14 14 0 0 0 5.9 5.9l1.6-2 4 1.5v3c0 .8-.7 1.5-1.5 1.5C11.1 19 5 12.9 5 5c0-.8.7-1.5 1.5-1.5z" />
                            </svg>
                            <span class="footer-contact-text">
                                Customer Care · Mon–Sat
                            </span>
                        </div>

                    </div>

                </div>

            </div>


            <div class="footer-bottom">

                <p class="footer-text">
                    Shop Easier, Live Better. © {{ date('Y') }} ShopEase.
                </p>

                <div class="footer-legal">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Cookie Policy</a>
                </div>

            </div>

        </div>

    </footer>



    <!-- =========================================================
         AUTH MODAL
         LOGIN + SIGNUP ONLY
    ========================================================== -->

    <div
        class="login-modal"
        id="loginModal"
        aria-hidden="true"
        aria-busy="false"
    >


        <div
            class="login-modal-overlay"
            id="loginModalOverlay"
        ></div>


        <div class="login-modal-box">


            <button
                type="button"
                class="login-modal-close"
                id="closeLoginModal"
                aria-label="Close authentication modal"
            >

                &times;

            </button>


            <iframe
                src="about:blank"
                id="loginModalIframe"
                class="login-modal-iframe"
                title="ShopEase Authentication"
            ></iframe>

        </div>

    </div>

</div>



<!-- =========================================================
     JAVASCRIPT
========================================================== -->

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /* =====================================================
           HERO VISUAL MOTION
        ====================================================== */

        const heroVisual =
            document.querySelector(
                '.hero-visual'
            );


        const visualCard =
            document.getElementById(
                'visualCard'
            );


        if (
            heroVisual &&
            visualCard
        ) {

            const supportsDesktopMotion =
                window.matchMedia(
                    '(min-width: 1051px)'
                ).matches;


            let targetMouseX = 0;

            let targetMouseY = 0;

            let mouseX = 0;

            let mouseY = 0;


            if (
                supportsDesktopMotion
            ) {

                heroVisual.addEventListener(
                    'pointermove',
                    function (event) {

                        const rect =
                            heroVisual.getBoundingClientRect();


                        const x =
                            event.clientX -
                            rect.left -
                            rect.width /
                            2;


                        const y =
                            event.clientY -
                            rect.top -
                            rect.height /
                            2;


                        const normalizedX =
                            Math.max(
                                -1,
                                Math.min(
                                    1,
                                    x /
                                    (
                                        rect.width /
                                        2
                                    )
                                )
                            );


                        const normalizedY =
                            Math.max(
                                -1,
                                Math.min(
                                    1,
                                    y /
                                    (
                                        rect.height /
                                        2
                                    )
                                )
                            );


                        targetMouseX =
                            normalizedX *
                            7;


                        targetMouseY =
                            normalizedY *
                            -5;

                    }
                );


                heroVisual.addEventListener(
                    'pointerleave',
                    function () {

                        targetMouseX = 0;

                        targetMouseY = 0;

                    }
                );

            }


            const startTime =
                performance.now();


            function animateVisual(
                currentTime
            ) {

                const elapsed =
                    (
                        currentTime -
                        startTime
                    ) /
                    1000;


                mouseX +=
                    (
                        targetMouseX -
                        mouseX
                    )
                    *
                    0.055;


                mouseY +=
                    (
                        targetMouseY -
                        mouseY
                    )
                    *
                    0.055;


                const waveA =
                    Math.sin(
                        elapsed *
                        1.05
                    );


                const waveB =
                    Math.sin(
                        elapsed *
                        0.67
                        +
                        1.25
                    );


                const waveC =
                    Math.cos(
                        elapsed *
                        0.88
                        -
                        0.55
                    );


                const baseX =
                    (
                        waveA *
                        7
                    )
                    +
                    (
                        waveB *
                        2.5
                    );


                const baseY =
                    waveC *
                    2.8;


                const rotateY =
                    (
                        waveA *
                        3.8
                    )
                    +
                    (
                        waveB *
                        1.2
                    )
                    +
                    (
                        mouseX *
                        0.32
                    );


                const rotateX =
                    (
                        waveC *
                        1.4
                    )
                    +
                    (
                        mouseY *
                        0.24
                    );


                const skewX =
                    (
                        waveA *
                        1.9
                    )
                    +
                    (
                        waveB *
                        0.9
                    )
                    +
                    (
                        mouseX *
                        0.16
                    );


                const scaleX =
                    1 +
                    (
                        waveB *
                        0.010
                    );


                const scaleY =
                    1 -
                    (
                        waveC *
                        0.004
                    );


                visualCard.style.transform =
                    `
                    perspective(1200px)
                    translate3d(
                        ${baseX + mouseX}px,
                        ${baseY + mouseY}px,
                        0
                    )
                    rotateY(
                        ${rotateY}deg
                    )
                    rotateX(
                        ${rotateX}deg
                    )
                    skewX(
                        ${skewX}deg
                    )
                    scaleX(
                        ${scaleX}
                    )
                    scaleY(
                        ${scaleY}
                    )
                    `;


                requestAnimationFrame(
                    animateVisual
                );

            }


            requestAnimationFrame(
                animateVisual
            );

        }



        /* =====================================================
           START SHOPPING
           SMOOTH SCROLL
        ====================================================== */

        const startShoppingButton =
            document.getElementById(
                'startShoppingButton'
            );


        const guestShopSection =
            document.getElementById(
                'guestShop'
            );


        if (
            startShoppingButton &&
            guestShopSection
        ) {

            startShoppingButton.addEventListener(
                'click',
                function (event) {

                    event.preventDefault();


                    guestShopSection.scrollIntoView(
                        {
                            behavior:
                                'smooth',

                            block:
                                'start'
                        }
                    );

                }
            );

        }



        /* =====================================================
           GUEST SEARCH
        ====================================================== */

        const searchInput =
            document.getElementById(
                'guestSearch'
            );


        const productCards =
            document.querySelectorAll(
                '.guest-product-card'
            );


        const noResults =
            document.getElementById(
                'guestNoResults'
            );


        if (
            searchInput &&
            productCards.length
        ) {

            searchInput.addEventListener(
                'input',
                function () {

                    const searchValue =
                        this.value
                            .trim()
                            .toLowerCase();


                    let visibleProducts =
                        0;


                    productCards.forEach(
                        function (card) {

                            const name =
                                (
                                    card.dataset.name ||
                                    ''
                                ).toLowerCase();


                            const category =
                                (
                                    card.dataset.category ||
                                    ''
                                ).toLowerCase();


                            const matches =
                                name.includes(
                                    searchValue
                                )
                                ||
                                category.includes(
                                    searchValue
                                );


                            if (matches) {

                                card.style.display =
                                    '';

                                visibleProducts++;

                            } else {

                                card.style.display =
                                    'none';

                            }

                        }
                    );


                    if (noResults) {

                        noResults.style.display =
                            visibleProducts === 0
                                ? 'block'
                                : 'none';

                    }

                }
            );

        }



        /* =====================================================
           WHY SHOPEASE — SCROLL REVEAL
        ====================================================== */

        const easeRevealTargets =
            document.querySelectorAll(
                '#whyShopease .ease-heading, #whyShopease .ease-card, #whyShopease .ease-tracking'
            );


        if (
            easeRevealTargets.length &&
            'IntersectionObserver' in window
        ) {

            const easeObserver =
                new IntersectionObserver(
                    function (entries) {

                        entries.forEach(
                            function (entry, index) {

                                if (
                                    entry.isIntersecting
                                ) {

                                    const delay =
                                        entry.target.classList.contains(
                                            'ease-card'
                                        )
                                            ? Array.from(
                                                  document.querySelectorAll(
                                                      '#whyShopease .ease-card'
                                                  )
                                              ).indexOf(entry.target) * 90
                                            : 0;


                                    setTimeout(
                                        function () {

                                            entry.target.classList.add(
                                                'in-view'
                                            );

                                        },
                                        delay
                                    );


                                    easeObserver.unobserve(
                                        entry.target
                                    );

                                }

                            }
                        );

                    },
                    {
                        threshold: 0.18,
                        rootMargin: '0px 0px -60px 0px'
                    }
                );


            easeRevealTargets.forEach(
                function (target) {

                    easeObserver.observe(
                        target
                    );

                }
            );

        } else {

            easeRevealTargets.forEach(
                function (target) {

                    target.classList.add(
                        'in-view'
                    );

                }
            );

        }



        /* =====================================================
           GENERAL SECTION SCROLL REVEAL
           Guest Shop → Product Cards → Why ShopEase → Footer
        ====================================================== */

        const scrollRevealTargets =
            document.querySelectorAll(
                '#guestShop .guest-shop-heading, ' +
                '#guestShop .guest-search-wrapper, ' +
                '#guestShop .guest-product-card, ' +
                '#whyShopease .ease-heading, ' +
                '#whyShopease .ease-card'
            );

        function prepareScrollReveal() {
            scrollRevealTargets.forEach(function (element) {
                element.classList.add('scroll-reveal');

                if (
                    element.classList.contains('guest-product-card') ||
                    element.classList.contains('ease-card')
                ) {
                    const parent = element.parentElement;
                    const siblingCards = parent
                        ? Array.from(parent.children).filter(function (child) {
                            return (
                                child.classList.contains('guest-product-card') ||
                                child.classList.contains('ease-card')
                            );
                        })
                        : [];

                    const cardIndex = siblingCards.indexOf(element);

                    if (cardIndex >= 0) {
                        element.style.transitionDelay = (cardIndex * 90) + 'ms';
                    }
                }
            });
        }

        prepareScrollReveal();

        if (
            scrollRevealTargets.length &&
            'IntersectionObserver' in window
        ) {
            const scrollRevealObserver =
                new IntersectionObserver(
                    function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('is-visible');
                            } else {
                                entry.target.classList.remove('is-visible');
                            }
                        });
                    },
                    {
                        threshold: 0.12,
                        rootMargin: '0px 0px -55px 0px'
                    }
                );

            scrollRevealTargets.forEach(function (target) {
                scrollRevealObserver.observe(target);
            });
        } else {
            scrollRevealTargets.forEach(function (target) {
                target.classList.add('is-visible');
            });
        }


        /* =====================================================
           LIVE PARCEL TRACKING ANIMATION
           The progress line is measured against the actual dot
           positions, so it ends exactly at the Delivered dot.
           The Delivered dot lights up only when the line reaches it.
        ====================================================== */

        const parcelRoute =
            document.querySelector(
                '.ease-parcel-route'
            );

        const parcelLine =
            parcelRoute
                ? parcelRoute.querySelector(
                    '.ease-parcel-line'
                )
                : null;

        const parcelProgressLine =
            parcelRoute
                ? parcelRoute.querySelector(
                    '.ease-parcel-progress-line'
                )
                : null;

        const parcelSteps =
            parcelRoute
                ? Array.from(
                    parcelRoute.querySelectorAll(
                        '.ease-parcel-step'
                    )
                )
                : [];

        const deliveredStep =
            parcelSteps.length
                ? parcelSteps[parcelSteps.length - 1]
                : null;


        let trackerStart = 0;
        let trackerEnd = 0;
        let trackerDistance = 0;
        let trackerResizeObserver = null;
        let trackerAnimationFrame = null;
        let trackerStartedAt = performance.now();
        const trackerDuration = 6000;


        function updateParcelLineGeometry() {

            if (
                !parcelRoute ||
                !parcelLine ||
                !parcelProgressLine ||
                !parcelSteps.length
            ) {
                return;
            }

            const firstDot =
                parcelSteps[0]
                    .querySelector(
                        '.ease-parcel-dot'
                    );

            const lastDot =
                deliveredStep
                    ? deliveredStep.querySelector(
                        '.ease-parcel-dot'
                    )
                    : null;

            if (!firstDot || !lastDot) {
                return;
            }

            const routeRect =
                parcelRoute.getBoundingClientRect();

            const firstRect =
                firstDot.getBoundingClientRect();

            const lastRect =
                lastDot.getBoundingClientRect();

            trackerStart =
                (
                    firstRect.top +
                    firstRect.height / 2
                ) -
                routeRect.top;

            trackerEnd =
                (
                    lastRect.top +
                    lastRect.height / 2
                ) -
                routeRect.top;

            trackerDistance =
                Math.max(
                    0,
                    trackerEnd - trackerStart
                );

            parcelLine.style.top =
                trackerStart + 'px';

            parcelLine.style.height =
                trackerDistance + 'px';

            parcelProgressLine.style.top =
                trackerStart + 'px';

            parcelProgressLine.style.height =
                '0px';

        }


        function easeInOutCubic(t) {

            return t < 0.5
                ? 4 * t * t * t
                : 1 -
                    Math.pow(
                        -2 * t + 2,
                        3
                    ) /
                    2;

        }


        function animateParcelTracking(
            currentTime
        ) {

            if (
                !parcelProgressLine ||
                !deliveredStep ||
                trackerDistance <= 0
            ) {
                return;
            }

            const elapsed =
                currentTime -
                trackerStartedAt;

            const loopedTime =
                elapsed %
                trackerDuration;

            const rawProgress =
                loopedTime /
                trackerDuration;

            const progress =
                easeInOutCubic(
                    rawProgress
                );

            const currentHeight =
                trackerDistance *
                progress;

            parcelProgressLine.style.height =
                currentHeight + 'px';

            /*
             * Delivered activates at the exact moment
             * the animated line reaches its endpoint.
             */
            const deliveredReached =
                progress >= 0.995;

            const alreadyReached =
                deliveredStep.classList.contains(
                    'delivered-reached'
                );

            if (
                deliveredReached &&
                !alreadyReached
            ) {

                deliveredStep.classList.add(
                    'delivered-reached'
                );

            } else if (
                !deliveredReached &&
                alreadyReached
            ) {

                deliveredStep.classList.remove(
                    'delivered-reached'
                );

            }

            trackerAnimationFrame =
                requestAnimationFrame(
                    animateParcelTracking
                );

        }


        if (
            parcelRoute &&
            parcelProgressLine &&
            deliveredStep
        ) {

            updateParcelLineGeometry();

            trackerStartedAt =
                performance.now();

            trackerAnimationFrame =
                requestAnimationFrame(
                    animateParcelTracking
                );

            window.addEventListener(
                'resize',
                function () {

                    updateParcelLineGeometry();

                },
                { passive: true }
            );

            if (
                'ResizeObserver' in window
            ) {

                trackerResizeObserver =
                    new ResizeObserver(
                        updateParcelLineGeometry
                    );

                trackerResizeObserver.observe(
                    parcelRoute
                );

            }

            window.addEventListener(
                'load',
                updateParcelLineGeometry,
                { once: true }
            );

        }


        /* =====================================================
           AUTH MODAL
        ====================================================== */

        const loginModal =
            document.getElementById(
                'loginModal'
            );


        const loginModalIframe =
            document.getElementById(
                'loginModalIframe'
            );


        const openLoginModal =
            document.getElementById(
                'openLoginModal'
            );


        const openSignupModal =
            document.getElementById(
                'openSignupModal'
            );


        const closeLoginModal =
            document.getElementById(
                'closeLoginModal'
            );


        const loginModalOverlay =
            document.getElementById(
                'loginModalOverlay'
            );



        /* =====================================================
           AUTH URLS
        ====================================================== */

        const loginUrl =
            @json(route('login'));


        const registerUrl =
            @json(route('register'));



        /* =====================================================
           CHECK AUTH PAGE
        ====================================================== */

        function isAllowedAuthPage(
            url
        ) {

            try {

                const current =
                    new URL(
                        url,
                        window.location.origin
                    );


                const login =
                    new URL(
                        loginUrl,
                        window.location.origin
                    );


                const register =
                    new URL(
                        registerUrl,
                        window.location.origin
                    );


                const currentPath =
                    current.pathname;


                const loginPath =
                    login.pathname;


                const registerPath =
                    register.pathname;


                return (
                    currentPath === loginPath
                    ||
                    currentPath === registerPath
                );

            } catch (
                error
            ) {

                return false;

            }

        }



        /*
         * Prevent duplicate full-screen handoffs.
         */
        let authTransitionStarted = false;


        /*
         * Keep the iframe from reacting to its initial
         * about:blank load or an intentional modal close.
         */
        let authModalOpening = false;



        /* =====================================================
           OPEN AUTH MODAL
        ====================================================== */

        function openAuthModal(
            authType
        ) {

            if (
                !loginModal ||
                !loginModalIframe
            ) {

                return;

            }


            const targetUrl =
                authType === 'signup'
                    ? registerUrl
                    : loginUrl;


            authTransitionStarted =
                false;


            authModalOpening =
                true;


            loginModal.classList.remove(
                'is-leaving'
            );


            loginModalIframe.src =
                targetUrl;


            loginModal.classList.add(
                'show'
            );


            loginModal.setAttribute(
                'aria-hidden',
                'false'
            );


            loginModal.setAttribute(
                'aria-busy',
                'true'
            );


            document.body.style.overflow =
                'hidden';


            /*
             * Give the browser one paint cycle so the
             * hidden → visible transition is rendered cleanly.
             */
            requestAnimationFrame(
                function () {

                    requestAnimationFrame(
                        function () {

                            authModalOpening =
                                false;

                        }
                    );

                }
            );

        }



        /* =====================================================
           CLOSE AUTH MODAL
        ====================================================== */

        function closeAuthModal() {

            if (
                !loginModal
            ) {

                return;

            }


            authTransitionStarted =
                false;


            authModalOpening =
                false;


            loginModal.classList.remove(
                'is-leaving'
            );


            loginModal.classList.remove(
                'show'
            );


            loginModal.setAttribute(
                'aria-hidden',
                'true'
            );


            loginModal.setAttribute(
                'aria-busy',
                'false'
            );


            document.body.style.overflow =
                '';


            /*
             * Reset the iframe only after the close
             * animation has completed.
             */
            if (
                loginModalIframe
            ) {

                setTimeout(
                    function () {

                        if (
                            !loginModal.classList.contains(
                                'show'
                            )
                        ) {

                            loginModalIframe.src =
                                'about:blank';

                        }

                    },
                    380
                );

            }

        }



        /* =====================================================
           SMOOTH FULL-SCREEN HANDOFF
        ====================================================== */

        function transitionToAuthDestination(
            destinationUrl
        ) {

            if (
                !loginModal ||
                !destinationUrl ||
                authTransitionStarted
            ) {

                return;

            }


            /*
             * Never redirect to blank pages.
             */
            if (
                destinationUrl ===
                'about:blank'
            ) {

                return;

            }


            authTransitionStarted =
                true;


            authModalOpening =
                false;


            loginModal.classList.add(
                'is-leaving'
            );


            loginModal.setAttribute(
                'aria-busy',
                'true'
            );


            /*
             * Keep the current page frozen while
             * the modal expands into the viewport.
             */
            document.body.style.overflow =
                'hidden';


            /*
             * Allow CSS transition to finish,
             * then let Laravel's destination page
             * take over the parent window.
             */
            setTimeout(
                function () {

                    window.location.assign(
                        destinationUrl
                    );

                },
                430
            );

        }



        /* =====================================================
           LOGIN BUTTON
        ====================================================== */

        if (
            openLoginModal
        ) {

            openLoginModal.addEventListener(
                'click',
                function () {

                    openAuthModal(
                        'login'
                    );

                }
            );

        }



        /* =====================================================
           SIGNUP BUTTON
        ====================================================== */

        if (
            openSignupModal
        ) {

            openSignupModal.addEventListener(
                'click',
                function () {

                    openAuthModal(
                        'signup'
                    );

                }
            );

        }



        /* =====================================================
           CLOSE BUTTON
        ====================================================== */

        if (
            closeLoginModal
        ) {

            closeLoginModal.addEventListener(
                'click',
                function () {

                    if (
                        authTransitionStarted
                    ) {

                        return;

                    }

                    closeAuthModal();

                }
            );

        }



        /* =====================================================
           CLICK OUTSIDE
        ====================================================== */

        if (
            loginModalOverlay
        ) {

            loginModalOverlay.addEventListener(
                'click',
                function () {

                    if (
                        authTransitionStarted
                    ) {

                        return;

                    }

                    closeAuthModal();

                }
            );

        }



        /* =====================================================
           IMPORTANT:
           DETECT WHEN IFRAME LEAVES AUTH PAGE
        ====================================================== */

        if (
            loginModalIframe
        ) {

            loginModalIframe.addEventListener(
                'load',
                function () {

                    /*
                     * Ignore iframe loads when the modal
                     * is not currently visible.
                     */
                    if (
                        !loginModal.classList.contains(
                            'show'
                        )
                    ) {

                        return;

                    }


                    /*
                     * Ignore the first paint while the
                     * modal is still opening.
                     */
                    if (
                        authModalOpening
                    ) {

                        return;

                    }


                    try {

                        const iframeUrl =
                            loginModalIframe
                                .contentWindow
                                .location
                                .href;


                        /*
                         * Ignore blank iframe states.
                         */
                        if (
                            !iframeUrl ||
                            iframeUrl ===
                                'about:blank'
                        ) {

                            return;

                        }


                        /*
                         * Login and signup pages are
                         * intentionally kept inside the modal.
                         */
                        if (
                            isAllowedAuthPage(
                                iframeUrl
                            )
                        ) {

                            loginModal.setAttribute(
                                'aria-busy',
                                'false'
                            );

                            return;

                        }


                        /*
                         * Once Laravel moves outside the
                         * authentication pages (for example:
                         *
                         * /buyer/register
                         * /seller/register
                         * /dashboard
                         * /buyer/dashboard
                         * /seller/dashboard
                         * etc.
                         *
                         * smoothly expand the modal to full
                         * screen before handing control to
                         * the parent page.
                         */
                        transitionToAuthDestination(
                            iframeUrl
                        );

                    } catch (
                        error
                    ) {

                        /*
                         * Ignore iframe access errors.
                         * Same-origin Laravel pages should normally
                         * be accessible here.
                         */

                    }

                }
            );

        }



        /* =====================================================
           ESC KEY
        ====================================================== */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key ===
                    'Escape'
                ) {

                    if (
                        authTransitionStarted
                    ) {

                        return;

                    }

                    closeAuthModal();

                }

            }
        );

    }
);

</script>

</body>

</html>