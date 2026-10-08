{{-- =========================================================
     BUYER NAVBAR / HEADER

     resources/views/components/buyer/navbar.blade.php

     UPDATED:
     - Search bar centered
     - Search bar made longer
     - Notification icon enlarged to match adjacent header icons
     - Buyer profile white circular background removed
     - Search button no longer has a maroon square
     - Search icon is now maroon
     - Existing dropdown/navigation behavior preserved
========================================================= --}}

@vite([
    'resources/css/buyer/components/navbar.css',
    'resources/js/buyer/components/navbar.js',
])

<header
    id="buyer-navbar"
    class="
        fixed
        top-0
        left-0
        right-0
        z-[100]
        w-full
    "
>

    {{-- =====================================================
         MAIN BUYER NAVBAR
    ====================================================== --}}
    <div
        id="buyer-main-header"
        class="
            relative
            h-[70px]
            bg-[#650B12]
            flex
            items-center
            overflow-visible
            border-none
        "
    >
        <div
            id="buyer-main-header-inner"
            class="
                relative
                w-full
                px-[22px]
                flex
                items-center
                gap-[28px]
            "
        >

            {{-- SHOP EASE LOGO --}}
            <a
                href="{{ url('/buyer/dashboard') }}"
                class="
                    buyer-navbar-logo
                    shrink-0
                    flex
                    items-center
                    no-underline
                "
                aria-label="ShopEase Home"
            >
                <img
                    src="{{ asset('icons/admin/dashboard/sidebar&navbar/shopease.png') }}"
                    alt="ShopEase"
                    class="
                        buyer-navbar-logo-image
                        block
                        object-contain
                    "
                >
            </a>

            {{-- SEARCH BAR --}}
            <div class="buyer-navbar-search min-w-0">
                <div
                    class="
                        buyer-search-shell
                        w-full
                        h-[38px]
                        bg-white
                        rounded-[8px]
                        overflow-hidden
                        flex
                        items-center
                    "
                >

                    {{-- SEARCH INPUT --}}
                    <div
                        class="
                            flex-1
                            min-w-0
                            h-full
                            flex
                            items-center
                        "
                    >
                        <input
                            type="text"
                            id="buyerSearchInput"
                            placeholder="Search products, or shop"
                            autocomplete="off"
                            class="
                                w-full
                                h-full
                                px-[20px]
                                border-none
                                outline-none
                                bg-transparent
                                text-[10px]
                                text-[#3E3735]
                                placeholder:text-[#A59F9D]
                            "
                        >
                    </div>

                    {{-- SEARCH BUTTON / NO MAROON SQUARE --}}
                    <button
                        type="button"
                        id="buyerSearchButton"
                        class="
                            h-[38px]
                            w-[48px]
                            shrink-0
                            flex
                            items-center
                            justify-center
                            border-none
                            bg-transparent
                            text-[#650B12]
                            cursor-pointer
                            transition-all
                            duration-200
                            hover:opacity-70
                            active:scale-[0.97]
                        "
                        aria-label="Search"
                    >
                        <svg
                            class="w-[19px] h-[19px]"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="M20 20l-4-4"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- RIGHT SIDE ACTIONS --}}
            <div
                class="
                    buyer-navbar-actions
                    shrink-0
                    flex
                    items-center
                    gap-[18px]
                "
            >

                {{-- NOTIFICATIONS --}}
                <button
                    type="button"
                    id="buyerNotificationButton"
                    class="
                        buyer-header-icon-button
                        relative
                        w-[30px]
                        h-[38px]
                        flex
                        items-center
                        justify-center
                    "
                    aria-label="Notifications"
                >
                    <img
                        src="{{ asset('icons/buyer/notification-header.png') }}"
                        alt=""
                        class="
                            buyer-notification-icon
                            w-[26px]
                            h-[26px]
                            object-contain
                            block
                        "
                    >

                    <span class="buyer-icon-badge" id="buyerNotificationBadge" style="display: none"></span>
                </button>

                {{-- CART --}}
                <a
                    href="{{ route('buyer.cart') }}"
                    id="buyerCartButton"
                    class="
                        buyer-header-icon-button
                        relative
                        w-[30px]
                        h-[38px]
                        flex
                        items-center
                        justify-center
                    "
                    aria-label="Shopping Cart"
                >
                    <img
                        src="{{ asset('icons/buyer/cart-header.png') }}"
                        alt=""
                        class="
                            w-[26px]
                            h-[26px]
                            object-contain
                            block
                        "
                    >

                    <span class="buyer-icon-badge">2</span>
                </a>

                {{-- BUYER ACCOUNT --}}
                <div class="buyer-account-wrapper">
                    <button
                        type="button"
                        id="buyerAccountButton"
                        aria-haspopup="true"
                        aria-expanded="false"
                        class="
                            buyer-account-button
                            flex
                            items-center
                            gap-[9px]
                        "
                    >

                        {{-- PROFILE IMAGE / NO WHITE BACKGROUND --}}
                        <div
                            class="
                                buyer-profile-image
                                w-[34px]
                                h-[34px]
                                shrink-0
                                rounded-full
                                bg-transparent
                                overflow-hidden
                                flex
                                items-center
                                justify-center
                            "
                        >
                            <img
                                src="{{ asset('icons/buyer/profile.png') }}"
                                alt="Buyer"
                                class="
                                    w-full
                                    h-full
                                    object-contain
                                "
                            >
                        </div>

                        {{-- ACCOUNT TEXT --}}
                        <div
                            class="
                                buyer-account-text
                                flex
                                flex-col
                                items-start
                                leading-none
                                text-left
                            "
                        >
                            <span
                                class="
                                    text-white
                                    text-[13px]
                                    font-semibold
                                    whitespace-nowrap
                                "
                            >
                                Buyer
                            </span>

                            <span
                                class="
                                    mt-[4px]
                                    text-white/70
                                    text-[9px]
                                    font-normal
                                    whitespace-nowrap
                                "
                            >
                                Buyer Account
                            </span>
                        </div>

                        {{-- CHEVRON --}}
                        <svg
                            class="
                                buyer-account-chevron
                                w-[13px]
                                h-[13px]
                                ml-[2px]
                                text-white
                            "
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M7 10l5 5 5-5"></path>
                        </svg>
                    </button>

                    {{-- BUYER ACCOUNT DROPDOWN --}}
                    <div
                        id="buyerAccountDropdown"
                        class="buyer-account-dropdown hidden"
                        role="menu"
                        aria-label="Buyer account menu"
                    >
                        <a
                            href="#"
                            onclick="return false;"
                            class="buyer-account-menu-link"
                            role="menuitem"
                        >
                            <span class="buyer-account-menu-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle
                                        cx="12"
                                        cy="8"
                                        r="4"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    />
                                    <path
                                        d="M5 20c.8-4 3.1-6 7-6s6.2 2 7 6"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </span>

                            <span>My Account</span>
                        </a>

                        <a
                            href="{{ route('buyer.my-purchases') }}"
                            class="buyer-account-menu-link"
                            role="menuitem"
                        >
                            <span class="buyer-account-menu-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M6 4h12v16H6V4Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M9 8h6M9 12h6M9 16h4"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </span>

                            <span>My Purchases</span>
                        </a>

                        <div class="buyer-account-menu-divider"></div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf

                            <button
                                type="submit"
                                class="buyer-account-menu-link buyer-logout-link"
                                role="menuitem"
                            >
                                <span class="buyer-account-menu-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M10 5H5v14h5"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M13 8l4 4-4 4M17 12H9"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </span>

                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @unless($hideCategories ?? false)
    {{-- =====================================================
         CATEGORY NAVIGATION
    ====================================================== --}}
    <div
        id="buyer-category-header"
        class="
            h-[48px]
            bg-white
            border-b
            border-[#ECE7E4]
            shadow-[0_2px_8px_rgba(45,20,15,0.05)]
            flex
            items-center
        "
    >
        <div
            class="
                w-full
                px-[22px]
                flex
                items-center
                min-w-0
            "
        >

            {{-- ALL CATEGORIES BUTTON --}}
            <button
                type="button"
                id="buyerAllCategoriesButton"
                class="
                    h-full
                    w-[185px]
                    shrink-0
                    flex
                    items-center
                    border-r
                    border-[#E8E1DE]
                    text-left
                    text-[#561018]
                    text-[12px]
                    font-semibold
                "
            >
                <span id="allCategoriesText" class="whitespace-nowrap">All Categories</span>

                <svg
                    class="
                        w-[14px]
                        h-[14px]
                        ml-auto
                        mr-[15px]
                    "
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M7 10l5 5 5-5"></path>
                </svg>
            </button>

            {{-- CATEGORY NAVIGATION / AUTO-SCROLLING TICKER --}}
            <nav
                id="buyerCategoryNavigation"
                class="
                    h-full
                    min-w-0
                    flex-1
                    overflow-hidden
                "
                aria-label="Product categories"
            >
                <div class="buyer-category-ticker">
                    <div class="buyer-category-track">
                        <div class="buyer-category-group">
                            <a href="#" onclick="return false;" class="buyer-category-link">Pet Supplies</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Electronics &amp; Gadgets</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Women's Apparel</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Men's Apparel</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Kids and Baby</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Home &amp; Garden</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Sports and Outdoors</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Health and Beauty</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Books and Media</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Food and Gourmet</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Automotive &amp; Motorcycle</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Furniture and Office Equipment</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Jewelry and Watches</a>
                            <a href="#" onclick="return false;" class="buyer-category-link">Office and School Supplies</a>
                        </div>
                        <div class="buyer-category-group" aria-hidden="true">
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Pet Supplies</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Electronics &amp; Gadgets</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Women's Apparel</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Men's Apparel</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Kids and Baby</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Home &amp; Garden</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Sports and Outdoors</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Health and Beauty</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Books and Media</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Food and Gourmet</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Automotive &amp; Motorcycle</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Furniture and Office Equipment</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Jewelry and Watches</a>
                            <a href="#" onclick="return false;" class="buyer-category-link" tabindex="-1">Office and School Supplies</a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    {{-- =====================================================
         ALL CATEGORIES DROPDOWN
    ====================================================== --}}
    <div
        id="buyerCategoriesDropdown"
        class="
            buyer-categories-dropdown
            hidden
            absolute
            top-[118px]
                z-[110]
            w-[330px]
            rounded-[12px]
            bg-white
            border
            border-[#EAE3E0]
            shadow-[0_14px_40px_rgba(41,14,12,0.16)]
            p-[12px]
        "
    >
        <div class="grid grid-cols-2 gap-[4px]">
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Pet Supplies</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Electronics &amp; Gadgets</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Women's Apparel</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Men's Apparel</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Kids and Baby</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Home and Garden</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Sports and Outdoors</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Health and Beauty</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Books and Media</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Food and Gourmet</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Automotive &amp; Motorcycle</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Furniture and Office Equipment</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Jewelry and Watches</a>
            <a href="#" onclick="return false;" class="buyer-dropdown-category">Office and School Supplies</a>
        </div>
    </div>
    @endunless
</header>
