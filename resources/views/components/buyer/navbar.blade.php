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
                href="#"
                onclick="return false;"
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
                                px-[15px]
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

                {{-- CHAT --}}
                <button
                    type="button"
                    id="buyerChatButton"
                    class="
                        buyer-header-icon-button
                        relative
                        w-[30px]
                        h-[38px]
                        flex
                        items-center
                        justify-center
                    "
                    aria-label="Messages"
                >
                    <img
                        src="{{ asset('icons/buyer/chats-header.png') }}"
                        alt=""
                        class="
                            w-[24px]
                            h-[24px]
                            object-contain
                            block
                        "
                    >
                </button>

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

                    <span class="buyer-icon-badge">4</span>
                </button>

                {{-- CART --}}
                <button
                    type="button"
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
                </button>

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
                        <form action="{{ route('logout') }}" method="POST">
    @csrf

    <button
        type="submit"
        class="buyer-logout-link"
    >
        Logout
    </button>
</form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

            {{-- CATEGORY NAVIGATION --}}
            <nav
                id="buyerCategoryNavigation"
                class="
                    h-full
                    flex
                    items-center
                    overflow-hidden
                    min-w-0
                    flex-1
                "
            >
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

                <button
                    type="button"
                    id="buyerMoreCategories"
                    class="
                        buyer-category-link
                        flex
                        items-center
                        gap-[5px]
                    "
                >
                    More
                    <svg
                        class="w-[13px] h-[13px]"
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
</header>

<style>
    /* =========================================================
       NAVBAR BASE
    ========================================================= */
    #buyer-navbar {
        font-family: Poppins, sans-serif;
    }

    #buyer-main-header {
        width: 100%;
        height: 70px;
        background: linear-gradient(
    to right,
    #52070B 19%,
    #71231C 100%
);
        border: none;
        border-bottom-right-radius: 30px;
        box-sizing: border-box;
    }

    #buyer-main-header-inner {
        width: 100%;
        height: 100%;
        padding-left: 22px;
        padding-right: 22px;
        box-sizing: border-box;
    }

    /* =========================================================
       SHOP EASE LOGO
    ========================================================= */
    .buyer-navbar-logo {
        width: 190px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        flex: 0 0 190px;
    }

    .buyer-navbar-logo-image {
        width: 160px;
        height: auto;
        margin-left: 40px;
        object-fit: contain;
        object-position: left center;
    }

    /* =========================================================
       SEARCH
       Centered + longer on desktop
    ========================================================= */
    .buyer-navbar-search {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 540px;
        max-width: 42vw;
        min-width: 300px;
    }

    .buyer-search-shell {
        box-sizing: border-box;
        border: none;
    }

    /* Search button is transparent; icon itself is maroon */
    #buyerSearchButton {
        color: #650B12;
        background: transparent !important;
    }

    #buyerSearchButton svg {
        stroke: currentColor;
    }

    /* =========================================================
       RIGHT ACTIONS
    ========================================================= */
    .buyer-navbar-actions {
        margin-left: auto;
    }

    .buyer-header-icon-button {
        padding: 0;
        border: none;
        background: transparent;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.18s ease, transform 0.18s ease;
    }

    .buyer-header-icon-button:hover {
        opacity: 0.82;
        transform: translateY(-1px);
    }

    .buyer-header-icon-button:active {
        transform: scale(0.96);
    }

    /* Notification icon is intentionally 26x26, matching the cart icon */
    .buyer-notification-icon {
        width: 30px !important;
        height: auto !important;
        object-fit: contain;
        transform: translateY(-3px);
    }

    .buyer-icon-badge {
        position: absolute;
        right: -2px;
        top: 0;
        min-width: 16px;
        height: 16px;
        padding: 0 4px;
        border-radius: 999px;
        background: #FF3434;
        color: white;
        font-size: 9px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    /* =========================================================
       PROFILE
       No white background/border on the profile container
    ========================================================= */
    .buyer-account-button {
        padding: 0;
        border: none;
        background: transparent;
        color: white;
        cursor: pointer;
    }

    .buyer-account-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .buyer-account-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 145px;
        padding: 7px;
        border: 1px solid #EAE3E0;
        border-radius: 10px;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(41, 14, 12, 0.16);
        z-index: 130;
    }

    .buyer-logout-link {
        min-height: 38px;
        display: flex;
        align-items: center;
        padding: 0 12px;
        border-radius: 7px;
        color: #650B12;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.18s ease, color 0.18s ease;
    }

    .buyer-logout-link:hover {
        background: #FFF0EC;
        color: #8B1822;
    }

    .buyer-account-button.is-open .buyer-account-chevron {
        transform: rotate(180deg);
    }

    .buyer-profile-image {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }

    .buyer-profile-image img {
        background: transparent !important;
        mix-blend-mode: normal;
    }

    .buyer-account-chevron {
        transition: transform 0.18s ease;
    }

    .buyer-account-button:hover .buyer-account-chevron {
        transform: translateY(2px);
    }

    /* =========================================================
       CATEGORY HEADER
    ========================================================= */
    #buyer-category-header {
        width: 100%;
    }

    .buyer-category-link {
        height: 100%;
        padding: 0 17px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #3D3431;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
        text-decoration: none;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: color 0.18s ease;
    }

    .buyer-category-link:hover {
        color: #861B26;
    }

    #buyerAllCategoriesButton {
        margin-left: 50px;
        background: transparent;
        border-top: none;
        border-left: none;
        border-bottom: none;
        cursor: pointer;
    }

    #buyerAllCategoriesButton:hover {
        color: #861B26;
    }

    /* =========================================================
       DROPDOWN
    ========================================================= */
    .buyer-categories-dropdown {
        position: fixed;
        left: 0;
        top: 0;
        animation: buyerDropdownFade 0.16s ease-out;
    }

    @keyframes buyerDropdownFade {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .buyer-dropdown-category {
        min-height: 38px;
        display: flex;
        align-items: center;
        padding: 8px 10px;
        border-radius: 8px;
        color: #3E3532;
        font-size: 11px;
        text-decoration: none;
        transition: background 0.18s ease, color 0.18s ease;
    }

    .buyer-dropdown-category:hover {
        background: #FFF0EC;
        color: #8B1822;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */
    @media (max-width: 1200px) {
        #buyer-main-header-inner {
            padding-left: 18px;
            padding-right: 18px;
            gap: 18px;
        }

        .buyer-navbar-logo {
            width: 165px;
            flex-basis: 165px;
        }

        .buyer-navbar-search {
            width: 500px;
            max-width: 42vw;
        }

        .buyer-navbar-actions {
            gap: 13px;
        }

        .buyer-category-link {
            padding: 0 11px;
        }
    }

    @media (max-width: 980px) {
        .buyer-navbar-logo {
            width: 145px;
            flex-basis: 145px;
        }

        .buyer-navbar-logo-image {
            width: 125px;
            height: 46px;
        }

        .buyer-navbar-search {
            width: 430px;
            max-width: 40vw;
        }

        .buyer-account-text {
            display: none;
        }

        .buyer-account-chevron {
            margin-left: 0;
        }
    }

    @media (max-width: 820px) {
        #buyer-main-header {
            height: 64px;
            border-bottom-right-radius: 24px;
        }

        #buyer-main-header-inner {
            padding-left: 12px;
            padding-right: 12px;
            gap: 12px;
        }

        .buyer-navbar-logo {
            width: 125px;
            flex-basis: 125px;
        }

        .buyer-navbar-logo-image {
            width: 112px;
            height: 42px;
        }

        /* Return to normal flex sizing on smaller screens */
        .buyer-navbar-search {
            position: relative;
            left: auto;
            top: auto;
            transform: none;
            width: auto;
            max-width: none;
            min-width: 0;
            flex: 1;
        }

        .buyer-search-shell {
            height: 36px;
        }

        .buyer-search-shell input {
            font-size: 11px;
            padding-left: 11px;
        }

        .buyer-search-shell button {
            height: 36px;
            width: 42px;
        }

        .buyer-navbar-actions {
            gap: 7px;
        }

        .buyer-header-icon-button {
            width: 27px;
        }

        .buyer-header-icon-button img {
            width: 21px;
            height: 21px;
        }

        .buyer-notification-icon {
            width: 23px !important;
            height: 23px !important;
        }

        .buyer-profile-image {
            width: 20px;
            height: auto;
        }

        #buyer-category-header {
            height: 45px;
        }

        #buyerAllCategoriesButton {
            width: 150px;
            padding-left: 0;
        }

        .buyer-category-link {
            padding: 0 9px;
            font-size: 10px;
        }

        .buyer-category-link:nth-child(n + 7) {
            display: none;
        }

        .buyer-categories-dropdown {
            width: calc(100vw - 24px);
        }
    }

    @media (max-width: 560px) {
        #buyer-main-header {
            height: 60px;
            border-bottom-right-radius: 20px;
        }

        #buyer-main-header-inner {
            gap: 8px;
        }

        .buyer-navbar-logo {
            width: 105px;
            flex-basis: 105px;
        }

        .buyer-navbar-logo-image {
            width: 96px;
            height: 38px;
        }

        .buyer-search-shell {
            height: 34px;
        }

        .buyer-search-shell button {
            width: 36px;
            height: 34px;
        }

        .buyer-search-shell button svg {
            width: 16px;
            height: 16px;
        }

        .buyer-navbar-actions {
            gap: 4px;
        }

        .buyer-header-icon-button {
            width: 24px;
            height: 34px;
        }

        .buyer-header-icon-button img {
            width: 19px;
            height: 19px;
        }

        .buyer-notification-icon {
            width: 21px !important;
            height: 21px !important;
        }

        .buyer-profile-image {
            width: 32px;
            height: 32px;
        }

        .buyer-account-chevron {
            display: none;
        }

        #buyer-category-header {
            height: 42px;
        }

        #buyerAllCategoriesButton {
            width: 135px;
            font-size: 10px;
        }

        #buyerCategoryNavigation {
            display: none;
        }
    }

    /* =========================================================
       REDUCED MOTION
    ========================================================= */
    @media (prefers-reduced-motion: reduce) {
        .buyer-header-icon-button,
        .buyer-account-chevron,
        .buyer-category-link,
        .buyer-dropdown-category,
        .buyer-categories-dropdown {
            transition: none !important;
            animation: none !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =================================================
       ELEMENTS
    ================================================== */
    const allCategoriesButton =
        document.getElementById('buyerAllCategoriesButton');

    const allCategoriesText =
        document.getElementById('allCategoriesText');

    const categoriesDropdown =
        document.getElementById('buyerCategoriesDropdown');

    const moreCategoriesButton =
        document.getElementById('buyerMoreCategories');

    const searchButton =
        document.getElementById('buyerSearchButton');

    const searchInput =
        document.getElementById('buyerSearchInput');

    const accountButton =
        document.getElementById('buyerAccountButton');

    const accountDropdown =
        document.getElementById('buyerAccountDropdown');

    const notificationButton =
        document.getElementById('buyerNotificationButton');

    const chatButton =
        document.getElementById('buyerChatButton');

    const cartButton =
        document.getElementById('buyerCartButton');

    /* =================================================
       CATEGORY DROPDOWN
    ================================================== */
    function positionCategoriesDropdown() {
        if (!allCategoriesText || !categoriesDropdown) {
            return;
        }

        const textRect = allCategoriesText.getBoundingClientRect();

        categoriesDropdown.style.left = `${Math.round(textRect.left)}px`;
        categoriesDropdown.style.top = `${Math.round(textRect.bottom + 1)}px`;
    }

    function toggleCategoriesDropdown() {
        if (!categoriesDropdown) {
            return;
        }

        positionCategoriesDropdown();
        categoriesDropdown.classList.toggle('hidden');
    }

    allCategoriesButton?.addEventListener('click', function (event) {
        event.stopPropagation();
        positionCategoriesDropdown();
        toggleCategoriesDropdown();
    });

    moreCategoriesButton?.addEventListener('click', function (event) {
        event.stopPropagation();
        positionCategoriesDropdown();
        toggleCategoriesDropdown();
    });

    window.addEventListener('resize', function () {
        if (categoriesDropdown && !categoriesDropdown.classList.contains('hidden')) {
            positionCategoriesDropdown();
        }
    });

    /* =================================================
       CLICK OUTSIDE DROPDOWN
    ================================================== */
    document.addEventListener('click', function (event) {
        if (
            categoriesDropdown &&
            !categoriesDropdown.contains(event.target) &&
            !allCategoriesButton?.contains(event.target) &&
            !moreCategoriesButton?.contains(event.target)
        ) {
            categoriesDropdown.classList.add('hidden');
        }

        if (
            accountDropdown &&
            !accountDropdown.contains(event.target) &&
            !accountButton?.contains(event.target)
        ) {
            accountDropdown.classList.add('hidden');
            accountButton?.classList.remove('is-open');
            accountButton?.setAttribute('aria-expanded', 'false');
        }
    });

    /* =================================================
       SEARCH
    ================================================== */
    function runBuyerSearch() {
        const searchTerm = searchInput?.value?.trim();

        if (!searchTerm) {
            searchInput?.focus();
            return;
        }

        console.log('Buyer search:', searchTerm);
    }

    searchButton?.addEventListener('click', runBuyerSearch);

    searchInput?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            runBuyerSearch();
        }
    });

    /* =================================================
       HEADER BUTTONS
    ================================================== */
    notificationButton?.addEventListener('click', function () {
        console.log('Buyer notifications opened.');
    });

    chatButton?.addEventListener('click', function () {
        console.log('Buyer chat opened.');
    });

    cartButton?.addEventListener('click', function () {
        console.log('Buyer cart opened.');
    });

    accountButton?.addEventListener('click', function (event) {
        event.stopPropagation();

        if (!accountDropdown) {
            return;
        }

        const isOpen = !accountDropdown.classList.contains('hidden');

        accountDropdown.classList.toggle('hidden', isOpen);
        accountButton.classList.toggle('is-open', !isOpen);
        accountButton.setAttribute(
            'aria-expanded',
            isOpen ? 'false' : 'true'
        );
    });

    /* =================================================
       ESCAPE
    ================================================== */
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            categoriesDropdown?.classList.add('hidden');
            accountDropdown?.classList.add('hidden');
            accountButton?.classList.remove('is-open');
            accountButton?.setAttribute('aria-expanded', 'false');
        }
    });
});
</script>
