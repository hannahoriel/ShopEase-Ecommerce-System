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


    @vite(['resources/css/app.css', 'resources/css/landing-page.css', 'resources/js/landing-page.js'])


    

</head>


<body data-login-url="{{ route('login') }}" data-register-url="{{ route('register') }}">

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



        <nav class="landing-nav-quick" aria-label="Explore ShopEase">
            <a href="#guestShop" data-landing-jump="guestShop">Shop</a>
            <a href="#whyShopease" data-landing-jump="whyShopease">Why ShopEase</a>
            <a href="#logisticsPartners" data-landing-jump="logisticsPartners">Logistics Partners</a>
            <a href="#mobileApp" data-landing-jump="mobileApp">Mobile App</a>
        </nav>

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




    <!-- Always-visible section shortcuts on phones and small tablets. -->
    

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


            <div class="se-role-intro">
                <p class="se-role-intro-caption">One marketplace. Four ways to be part of it.</p>
                <div class="se-role-links" aria-label="Explore ShopEase opportunities">
                    <a href="#guestShop" class="se-role-link" data-landing-jump="guestShop">
                        <span class="se-role-symbol"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 8h14l-1 12H6L5 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg></span><span>Buyer</span>
                    </a>
                    <a href="#mobileApp" class="se-role-link" data-landing-jump="mobileApp">
                        <span class="se-role-symbol"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9 5 4h14l2 5v3a2 2 0 0 1-4 0 2 2 0 0 1-4 0 2 2 0 0 1-4 0 2 2 0 0 1-4 0 2 2 0 0 1-2-2Z"/><path d="M5 14v6h14v-6M10 20v-5h4v5"/></svg></span><span>Seller</span>
                    </a>
                    <a href="#logisticsPartners" class="se-role-link" data-landing-jump="logisticsPartners">
                        <span class="se-role-symbol"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h11v11H3zM14 10h4l3 4v3h-7z"/><circle cx="7.5" cy="18" r="2"/><circle cx="17.5" cy="18" r="2"/></svg></span><span>Logistics</span>
                    </a>
                    <a href="#mobileApp" class="se-role-link" data-landing-jump="mobileApp">
                        <span class="se-role-symbol"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="5" cy="17" r="3"/><circle cx="19" cy="17" r="3"/><path d="m5 17 4-8h4l3 8h3M8 13h8l-2-4M13 9l2-3h3M7 7h3"/></svg></span><span>Rider</span>
                    </a>
                </div>
            </div>

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
         LOGISTICS PARTNERSHIP: dedicated information page CTA
    ========================================================== -->
    <section class="partner-section" id="logisticsPartners" aria-labelledby="partnerSectionTitle">
        <div class="partner-inner">
            <div class="partner-copy ux-reveal">
                <span class="partner-kicker">Grow with ShopEase</span>
                <h2 id="partnerSectionTitle">Your deliveries.<br><em>Our growing marketplace.</em></h2>
                <p>Is your logistics company looking for a new partnership opportunity? Explore becoming a ShopEase logistics partner and helping connect sellers, buyers, and deliveries.</p>
                <ul class="partner-benefits">
                    <li class="ux-reveal se-reveal-child"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg> Discover opportunities to serve ShopEase orders and customers.</li>
                    <li class="ux-reveal se-reveal-child"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg> Learn about company registration and partnership review.</li>
                    <li class="ux-reveal se-reveal-child"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg> Join an ecosystem built for shopping and delivery.</li>
                </ul>
                <p class="partner-subheading">How does partnering work?</p>
                <div class="partner-experience ux-reveal" aria-label="Logistics partnership process">
                    <details open><summary><span class="step-number">01</span>Explore the partnership <svg class="step-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></summary><p>Open the partner information page to see how ShopEase works with registered logistics companies.</p></details>
                    <details><summary><span class="step-number">02</span>Submit company information <svg class="step-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></summary><p>When registration is connected, your company can provide the details and documents required for a partnership request.</p></details>
                    <details><summary><span class="step-number">03</span>Wait for review <svg class="step-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></summary><p>ShopEase reviews submitted partnership requests. Registration does not guarantee approval.</p></details>
                </div>
                <div class="partner-actions">
                    <a class="partner-cta" href="{{ url('/logistics/landing') }}">
    Become a Logistics Partner
    <span aria-hidden="true">↗</span>
</a>
                    <a class="partner-text-link" href="{{ url('/logistics/landing') }}">
    How partnership works
</a>
                </div>
                <span class="partner-note">Partnership requests are subject to review and approval.</span>
            </div>
            <div class="partner-art ux-reveal" aria-hidden="true">
                <div class="partner-art-base">
                    <span class="partner-art-label">SHOPEASE PARTNER NETWORK</span>
                    <h3>Let's move more together.</h3>
                    <svg viewBox="0 0 360 184" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="181" cy="164" rx="151" ry="15" fill="#30080D" fill-opacity=".29"/><rect x="23" y="65" width="191" height="85" rx="15" fill="#FFF1EA"/><rect x="32" y="74" width="173" height="67" rx="9" fill="#FFD8C9"/><rect x="50" y="88" width="80" height="8" rx="4" fill="#A52A2A"/><rect x="50" y="102" width="117" height="5" rx="2.5" fill="#C97467"/><rect x="50" y="112" width="100" height="5" rx="2.5" fill="#C97467"/><path d="M214 95H272L322 132V150H214V95Z" fill="#F8AF96"/><path d="M274 102V132H305L274 102Z" fill="#FFEDE6"/><path d="M238 106H261V132H238V106Z" fill="#FFEDE6"/><circle cx="91" cy="150" r="23" fill="#4C131B"/><circle cx="91" cy="150" r="11" fill="#FFF1EA"/><circle cx="271" cy="150" r="23" fill="#4C131B"/><circle cx="271" cy="150" r="11" fill="#FFF1EA"/><path d="M22 139H321" stroke="#FFCFBF" stroke-width="7" stroke-linecap="round"/></svg>
                    <div class="se-delivery-live" aria-label="Illustrative delivery journey">
                      <div class="se-delivery-live-top"><span><i></i> DELIVERY NETWORK</span><strong>On the move ↗</strong></div>
                      <div class="se-delivery-route"><span class="se-route-stop">Seller</span><span class="se-route-line"><i class="se-route-parcel">📦</i></span><span class="se-route-stop">Buyer</span></div>
                      <div class="se-delivery-caption"><span>Order picked up</span><span>Delivering with care</span></div>
                    </div>
                </div>
                <div class="partner-float one"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h11v10H3zM14 10h4l3 3v4h-7"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/></svg> Logistics partners</div>
                <div class="partner-float two"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg> Grow with every delivery</div>
            </div>
        </div>
    </section>

    <!-- =========================================================
         MOBILE APP: Android APK direct download
    ========================================================== -->
    <section class="mobile-section" id="mobileApp" aria-labelledby="mobileSectionTitle">
        <div class="mobile-inner">
            <div class="mobile-showcase ux-reveal" aria-hidden="true">
                <div class="mobile-glow"></div>
                <div class="mobile-device">
                    <div class="mobile-notch"></div>
                    <div class="se-app-demo" data-demo-stage="0" aria-label="Animated preview of browsing, cart, checkout and order tracking">
                      <div class="se-demo-bar"><strong>Shop<span>Ease</span></strong><span class="se-demo-status"><i></i> APP PREVIEW</span></div>
                      <div class="se-demo-screen se-demo-browse"><div class="se-demo-headline">What are we shopping for?</div><div class="se-demo-search">⌕ &nbsp; Search products...</div><div class="se-demo-grid"><div class="se-demo-product"><span class="se-demo-item-icon">🎧</span><b>Headphones</b><small>₱899</small></div><div class="se-demo-product"><span class="se-demo-item-icon">⌚</span><b>Smart Watch</b><small>₱1,299</small></div></div><div class="se-demo-float-action">+ Add to cart <span>✓</span></div></div>
                      <div class="se-demo-screen se-demo-cart"><div class="se-demo-headline">Your cart <span>2 items</span></div><div class="se-demo-cart-row"><span>🎧</span><div><b>Wireless Headphones</b><small>Qty 1 · ₱899</small></div><strong>✓</strong></div><div class="se-demo-cart-row"><span>⌚</span><div><b>Smart Watch</b><small>Qty 1 · ₱1,299</small></div><strong>✓</strong></div><div class="se-demo-total">Total <b>₱2,198</b></div><div class="se-demo-button">Proceed to Checkout →</div></div>
                      <div class="se-demo-screen se-demo-checkout"><div class="se-demo-headline">Secure checkout</div><div class="se-demo-check-card"><small>DELIVER TO</small><b>Home · Laguna, PH</b><span>Standard delivery</span></div><div class="se-demo-check-card"><small>PAYMENT METHOD</small><b>Cash on Delivery</b><span>Safe & convenient</span></div><div class="se-demo-total">Order total <b>₱2,198</b></div><div class="se-demo-button">Place Order ✓</div></div>
                      <div class="se-demo-screen se-demo-track"><div class="se-demo-success">✓</div><div class="se-demo-headline">Order placed!</div><p>Your ShopEase order is on its way.</p><div class="se-demo-order-code">Order #SE-2048</div><div class="se-demo-track-steps"><span class="done">● <b>Confirmed</b></span><span class="done">● <b>Preparing</b></span><span>○ <b>In transit</b></span></div></div>
                      <div class="se-demo-bottom"><div class="se-demo-progress"><i></i><i></i><i></i><i></i></div><span data-demo-caption>01 / 04 · Discover products</span></div>
                    </div>
                </div>
                <span class="mobile-chip buyer">✦ Shop anywhere</span>
                <span class="mobile-chip rider">↗ Rider applications</span>
                <span class="mobile-chip seller">▣ Manage your shop anywhere</span>
            </div>
            <div class="mobile-copy ux-reveal">
                <span class="mobile-kicker">ShopEase goes mobile</span>
                <h2 id="mobileSectionTitle">One app.<br>More ways to <span style="color:#b74d47">move forward.</span></h2>
                <p>Discover ShopEase on your phone—from browsing products and managing a shop to exploring opportunities as a delivery rider. Our mobile experience brings shoppers, sellers, and riders closer together.</p>
                <div class="mobile-feature-list">
                    <div class="mobile-feature ux-reveal se-reveal-child"><span class="mobile-feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 8h14l-1 12H6L5 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg></span><div><strong>Shop on your phone</strong><span>Browse products, place orders, and track purchases wherever you are.</span></div></div>
                    <div class="mobile-feature ux-reveal se-reveal-child"><span class="mobile-feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9 5 4h14l2 5v3a2 2 0 0 1-4 0 2 2 0 0 1-4 0 2 2 0 0 1-4 0 2 2 0 0 1-4 0 2 2 0 0 1-2-2Z"/><path d="M5 14v6h14v-6M10 20v-5h4v5"/></svg></span><div><strong>Sell with ShopEase</strong><span>Manage product listings, monitor orders, and check your store updates.</span></div></div>
                    <div class="mobile-feature ux-reveal se-reveal-child"><span class="mobile-feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="5" cy="17" r="3"/><circle cx="19" cy="17" r="3"/><path d="m5 17 4-8h4l3 8h3M8 13h8l-2-4M13 9l2-3h3M7 7h3"/></svg></span><div><strong>Apply as a rider</strong><span>Rider applications are offered exclusively through the ShopEase mobile application.</span></div></div>
                </div>
                <div class="mobile-actions">
                    <a href="https://shopeasemobileapk.blob.core.windows.net/app-downloads/ShopEase.apk" class="mobile-cta" aria-label="Download ShopEase Android APK"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v12m-5-5 5 5 5-5M4 17v4h16v-4"/></svg> Download ShopEase App</a>
                </div>
                <span class="mobile-availability">Android APK · Download and install on your Android device.</span>
            </div>
        </div>
    </section>

    <!-- =========================================================
         REALISTIC FOOTER
    ========================================================== -->

    @include('components.landing page.footer')



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




<!-- Mobile release information (buttons deliberately do not download a missing app). -->
<div class="landing-info-modal" id="mobileInfoModal" role="presentation" hidden>
  <div class="landing-info-dialog" role="dialog" aria-modal="true" aria-labelledby="mobileInfoTitle" aria-describedby="mobileInfoBody">
    <button class="landing-info-close" type="button" id="mobileInfoClose" aria-label="Close">×</button>
    <div class="landing-info-symbol"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M10 18h4"/></svg></div>
    <h2 id="mobileInfoTitle">Mobile App Coming Soon</h2>
    <p id="mobileInfoBody">The download link will be available once the ShopEase mobile app is ready.</p>
    <button type="button" class="mobile-cta" id="mobileInfoOkay">Got it</button>
  </div>
</div>





</body>

</html>