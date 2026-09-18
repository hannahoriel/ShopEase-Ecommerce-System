{{-- =========================================================
     SELLER NAVBAR
     resources/views/components/seller/navbar.blade.php

     ADMIN-MATCHED VISUAL / UX
     ---------------------------------------------------------
     - Same 88px navbar height
     - Same left/right spacing
     - Same burger sizing / animation
     - Same 21px page title
     - Same notification sizing / dropdown
     - Same profile card sizing / dropdown
     - Same hover colors / scale effects
     - Same sidebar-follow positioning
     - Seller-specific text, icons, and notifications preserved
========================================================= --}}

<header
    id="seller-navbar"
    class="
        fixed
        top-0
        left-0
        right-0
        z-10
        h-[88px]
        bg-[#FFE6DE]
        flex
        items-center
        justify-between
    "
>

    {{-- =====================================================
         LEFT SIDE
    ====================================================== --}}
    <div
        id="seller-navbar-left"
        class="
            ml-[304px]
            flex
            items-center
            gap-6
            transition-all
            duration-300
        "
    >

        {{-- Sidebar Toggle --}}
        <button
            id="seller-sidebar-toggle"
            type="button"
            class="
                text-maroon-900
                flex
                items-center
                justify-center
                transition-all
                duration-200
                hover:scale-110
                active:scale-95
            "
            aria-label="Toggle seller sidebar"
            aria-controls="sellerSidebar"
            aria-expanded="true"
        >
            <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                />
            </svg>
        </button>


        {{-- Page Title --}}
        <h1
            class="
                text-[21px]
                font-bold
                text-gray-800
                leading-none
                whitespace-nowrap
            "
        >
            @yield('page-title', 'Dashboard')
        </h1>

    </div>


    {{-- =====================================================
         RIGHT SIDE
    ====================================================== --}}
    <div
        class="
            flex
            items-center
            gap-6
            mr-[28px]
        "
    >

        {{-- =================================================
             NOTIFICATIONS
        ================================================== --}}
        <div class="relative">

            <button
                id="seller-notification-button"
                type="button"
                class="
                    relative
                    flex
                    items-center
                    justify-center
                    transition-transform
                    duration-200
                    hover:scale-105
                    active:scale-95
                "
                aria-label="Notifications"
                aria-expanded="false"
                aria-controls="seller-notification-dropdown"
            >
                <img
                    src="{{ asset('icons/seller/sidebar&navbar/notification.png') }}"
                    class="w-6 h-7 object-contain"
                    alt="Notifications"
                >

                <span
                    class="
                        absolute
                        -top-1.5
                        -right-1.5
                        bg-red-600
                        text-white
                        text-[10px]
                        font-semibold
                        rounded-full
                        w-[18px]
                        h-[18px]
                        flex
                        items-center
                        justify-center
                    "
                >
                    4
                </span>
            </button>


            {{-- Notification Dropdown --}}
            <div
                id="seller-notification-dropdown"
                class="
                    hidden
                    absolute
                    right-0
                    top-10
                    w-[290px]
                    bg-white
                    rounded-2xl
                    shadow-xl
                    border
                    border-gray-100
                    overflow-hidden
                    z-[60]
                "
            >
                <div
                    class="
                        px-4
                        py-3
                        border-b
                        border-gray-100
                        flex
                        items-center
                        justify-between
                    "
                >
                    <h3 class="text-[15px] font-bold text-gray-800">
                        Notifications
                    </h3>

                    <span class="text-[11px] text-maroon-700 font-medium">
                        4 new
                    </span>
                </div>


                <div class="max-h-[260px] overflow-y-auto">

                    <div
                        class="
                            px-4
                            py-3
                            hover:bg-gray-50
                            transition
                            cursor-pointer
                            border-b
                            border-gray-50
                        "
                    >
                        <p class="text-[13px] font-semibold text-gray-800">
                            New Order Received
                        </p>

                        <p class="text-[11px] text-gray-400 mt-1">
                            Order #ORD-2089 has been placed.
                        </p>
                    </div>


                    <div
                        class="
                            px-4
                            py-3
                            hover:bg-gray-50
                            transition
                            cursor-pointer
                            border-b
                            border-gray-50
                        "
                    >
                        <p class="text-[13px] font-semibold text-gray-800">
                            Shipment In Transit
                        </p>

                        <p class="text-[11px] text-gray-400 mt-1">
                            Order #ORD-2087 is now in transit.
                        </p>
                    </div>


                    <div
                        class="
                            px-4
                            py-3
                            hover:bg-gray-50
                            transition
                            cursor-pointer
                            border-b
                            border-gray-50
                        "
                    >
                        <p class="text-[13px] font-semibold text-gray-800">
                            Low Stock Alert
                        </p>

                        <p class="text-[11px] text-gray-400 mt-1">
                            Wireless Headphones has only 10 units left.
                        </p>
                    </div>


                    <div
                        class="
                            px-4
                            py-3
                            hover:bg-gray-50
                            transition
                            cursor-pointer
                            border-b
                            border-gray-50
                        "
                    >
                        <p class="text-[13px] font-semibold text-gray-800">
                            New Customer Feedback
                        </p>

                        <p class="text-[11px] text-gray-400 mt-1">
                            A customer left feedback on your product.
                        </p>
                    </div>

                </div>


                <div
                    class="
                        px-4
                        py-2.5
                        text-center
                        border-t
                        border-gray-100
                    "
                >
                    <a
                        href="#"
                        class="
                            text-[12px]
                            font-medium
                            text-maroon-700
                            hover:text-maroon-900
                            transition
                        "
                    >
                        View all notifications
                    </a>
                </div>
            </div>

        </div>


        {{-- =================================================
             SELLER PROFILE
        ================================================== --}}
        <div class="relative">

            <button
                id="seller-profile-button"
                type="button"
                class="
                    flex
                    items-center
                    gap-2.5
                    bg-white
                    rounded-[10px]
                    pl-2.5
                    pr-3
                    py-1.5
                    shadow-sm
                    border
                    border-gray-200
                    transition
                    hover:shadow-md
                    active:scale-[0.99]
                "
                aria-expanded="false"
                aria-controls="seller-profile-dropdown"
            >
                <img
                    src="{{ asset('icons/seller/sidebar&navbar/seller-profile.png') }}"
                    class="w-9 h-9 object-contain"
                    alt="Seller Profile"
                >

                <div class="text-sm min-w-[135px] text-left">
                    <p
                        class="
                            font-semibold
                            text-gray-800
                            leading-tight
                            text-[16px]
                        "
                    >
                        Seller
                    </p>

                    <p class="text-[12px] text-gray-400 leading-tight">
                        Seller Account
                    </p>
                </div>

                <svg
                    id="seller-profile-chevron"
                    class="
                        w-[18px]
                        h-[18px]
                        text-gray-700
                        transition-transform
                        duration-200
                    "
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
            </button>


            {{-- Profile Dropdown --}}
            <div
                id="seller-profile-dropdown"
                class="
                    hidden
                    absolute
                    right-0
                    top-[52px]
                    w-[225px]
                    bg-white
                    rounded-2xl
                    shadow-xl
                    border
                    border-gray-100
                    overflow-hidden
                    z-[60]
                "
            >
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-[14px] font-semibold text-gray-800">
                        Seller
                    </p>

                    <p class="text-[11px] text-gray-400 mt-1">
                        Seller Account
                    </p>
                </div>


                <div class="p-1.5">
                    <a
                        href="#"
                        class="
                            flex
                            items-center
                            gap-2.5
                            px-2.5
                            py-2.5
                            rounded-xl
                            text-[13px]
                            text-gray-700
                            hover:bg-[#FFF2EE]
                            transition
                        "
                    >
                        <span>My Account</span>
                    </a>

                    <a
                        href="#"
                        class="
                            flex
                            items-center
                            gap-2.5
                            px-2.5
                            py-2.5
                            rounded-xl
                            text-[13px]
                            text-gray-700
                            hover:bg-[#FFF2EE]
                            transition
                        "
                    >
                        <span>Settings</span>
                    </a>
                </div>


                <div class="border-t border-gray-100 p-1.5">
                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                        id="seller-navbar-logout-form"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="
                                w-full
                                flex
                                items-center
                                gap-2.5
                                px-2.5
                                py-2.5
                                rounded-xl
                                text-[13px]
                                text-red-600
                                hover:bg-red-50
                                transition
                                text-left
                            "
                        >
                            <span>Log Out</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

</header>


<style>
    /* =========================================================
       SELLER NAVBAR ↔ SELLER SIDEBAR
       Matches Admin navbar positioning.
    ========================================================== */

    #seller-navbar-left {
        margin-left: 304px;
    }

    body.sidebar-collapsed
    #seller-navbar-left,
    body.sidebar-hidden
    #seller-navbar-left,
    body.sidebar-closed
    #seller-navbar-left,
    body.seller-sidebar-collapsed
    #seller-navbar-left {
        margin-left: 128px !important;
    }


    @media (max-width: 900px) {
        #seller-navbar-left {
            margin-left: 104px !important;
        }

        body.sidebar-collapsed
        #seller-navbar-left,
        body.sidebar-hidden
        #seller-navbar-left,
        body.sidebar-closed
        #seller-navbar-left,
        body.seller-sidebar-collapsed
        #seller-navbar-left {
            margin-left: 24px !important;
        }
    }


    @media (max-width: 640px) {
        #seller-navbar-left {
            margin-left: 20px !important;
            gap: 12px;
        }

        #seller-navbar-left h1 {
            font-size: 18px;
        }

        #seller-navbar > div:last-child {
            margin-right: 14px;
            gap: 12px;
        }
    }
</style>


<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const sidebarToggle =
            document.getElementById(
                'seller-sidebar-toggle'
            );

        const navbarLeft =
            document.getElementById(
                'seller-navbar-left'
            );

        const sellerSidebar =
            document.getElementById(
                'sellerSidebar'
            );

        const notificationButton =
            document.getElementById(
                'seller-notification-button'
            );

        const notificationDropdown =
            document.getElementById(
                'seller-notification-dropdown'
            );

        const profileButton =
            document.getElementById(
                'seller-profile-button'
            );

        const profileDropdown =
            document.getElementById(
                'seller-profile-dropdown'
            );

        const profileChevron =
            document.getElementById(
                'seller-profile-chevron'
            );

        const navbarLogoutForm =
            document.getElementById(
                'seller-navbar-logout-form'
            );


        /* =====================================================
           NAVBAR POSITION
           Same target positions used by the Admin navbar.
        ====================================================== */

        function updateNavbarPosition() {
            if (!navbarLeft) {
                return;
            }

            const bodyCollapsed =
                document.body.classList.contains(
                    'sidebar-collapsed'
                )
                ||
                document.body.classList.contains(
                    'sidebar-hidden'
                )
                ||
                document.body.classList.contains(
                    'sidebar-closed'
                )
                ||
                document.body.classList.contains(
                    'seller-sidebar-collapsed'
                );

            const sidebarCollapsed =
                sellerSidebar
                &&
                (
                    sellerSidebar.classList.contains(
                        'w-20'
                    )
                    ||
                    sellerSidebar.classList.contains(
                        'w-[80px]'
                    )
                    ||
                    sellerSidebar.classList.contains(
                        'collapsed'
                    )
                    ||
                    sellerSidebar.classList.contains(
                        'sidebar-collapsed'
                    )
                    ||
                    sellerSidebar.classList.contains(
                        'seller-sidebar-collapsed'
                    )
                );

            if (
                bodyCollapsed
                ||
                sidebarCollapsed
            ) {
                navbarLeft.style.marginLeft =
                    '128px';
            } else {
                navbarLeft.style.marginLeft =
                    '304px';
            }

            if (sidebarToggle) {
                sidebarToggle.setAttribute(
                    'aria-expanded',
                    sidebarCollapsed
                        ? 'false'
                        : 'true'
                );
            }
        }


        updateNavbarPosition();


        /* =====================================================
           SIDEBAR TOGGLE
        ====================================================== */

        if (sidebarToggle) {
            sidebarToggle.addEventListener(
                'click',
                function () {

                    document.body.dispatchEvent(
                        new CustomEvent(
                            'toggle-seller-sidebar'
                        )
                    );

                    setTimeout(
                        updateNavbarPosition,
                        20
                    );

                    setTimeout(
                        updateNavbarPosition,
                        150
                    );

                    setTimeout(
                        updateNavbarPosition,
                        320
                    );
                }
            );
        }


        /* =====================================================
           WATCH SIDEBAR STATE
        ====================================================== */

        if (sellerSidebar) {
            const sidebarObserver =
                new MutationObserver(
                    function () {
                        updateNavbarPosition();
                    }
                );

            sidebarObserver.observe(
                sellerSidebar,
                {
                    attributes: true,
                    attributeFilter: [
                        'class',
                        'style'
                    ]
                }
            );
        }


        const bodyObserver =
            new MutationObserver(
                function () {
                    updateNavbarPosition();
                }
            );

        bodyObserver.observe(
            document.body,
            {
                attributes: true,
                attributeFilter: [
                    'class'
                ]
            }
        );


        /* =====================================================
           NOTIFICATIONS
        ====================================================== */

        function closeNotifications() {
            if (!notificationDropdown) {
                return;
            }

            notificationDropdown.classList.add(
                'hidden'
            );

            notificationButton?.setAttribute(
                'aria-expanded',
                'false'
            );
        }


        function closeProfile() {
            if (!profileDropdown) {
                return;
            }

            profileDropdown.classList.add(
                'hidden'
            );

            profileButton?.setAttribute(
                'aria-expanded',
                'false'
            );

            profileChevron?.classList.remove(
                'rotate-180'
            );
        }


        if (
            notificationButton
            &&
            notificationDropdown
        ) {
            notificationButton.addEventListener(
                'click',
                function (event) {
                    event.stopPropagation();

                    closeProfile();

                    const isHidden =
                        notificationDropdown
                            .classList
                            .contains(
                                'hidden'
                            );

                    notificationDropdown
                        .classList
                        .toggle(
                            'hidden'
                        );

                    notificationButton
                        .setAttribute(
                            'aria-expanded',
                            String(
                                isHidden
                            )
                        );
                }
            );
        }


        /* =====================================================
           PROFILE
        ====================================================== */

        if (
            profileButton
            &&
            profileDropdown
        ) {
            profileButton.addEventListener(
                'click',
                function (event) {
                    event.stopPropagation();

                    closeNotifications();

                    const isHidden =
                        profileDropdown
                            .classList
                            .contains(
                                'hidden'
                            );

                    profileDropdown
                        .classList
                        .toggle(
                            'hidden'
                        );

                    profileButton
                        .setAttribute(
                            'aria-expanded',
                            String(
                                isHidden
                            )
                        );

                    profileChevron
                        ?.classList
                        .toggle(
                            'rotate-180',
                            isHidden
                        );
                }
            );
        }


        /* =====================================================
           CLICK OUTSIDE
        ====================================================== */

        document.addEventListener(
            'click',
            function (event) {

                if (
                    notificationDropdown
                    &&
                    !notificationDropdown.contains(
                        event.target
                    )
                    &&
                    notificationButton
                    &&
                    !notificationButton.contains(
                        event.target
                    )
                ) {
                    closeNotifications();
                }

                if (
                    profileDropdown
                    &&
                    !profileDropdown.contains(
                        event.target
                    )
                    &&
                    profileButton
                    &&
                    !profileButton.contains(
                        event.target
                    )
                ) {
                    closeProfile();
                }
            }
        );


        /* =====================================================
           ESCAPE
        ====================================================== */

        document.addEventListener(
            'keydown',
            function (event) {
                if (
                    event.key !==
                    'Escape'
                ) {
                    return;
                }

                closeNotifications();
                closeProfile();
            }
        );


        /* =====================================================
           LOGOUT UX
        ====================================================== */

        if (navbarLogoutForm) {
            navbarLogoutForm.addEventListener(
                'submit',
                function () {
                    const button =
                        navbarLogoutForm.querySelector(
                            'button[type="submit"]'
                        );

                    if (!button) {
                        return;
                    }

                    button.disabled = true;

                    button.classList.add(
                        'opacity-70'
                    );

                    const label =
                        button.querySelector(
                            'span'
                        );

                    if (label) {
                        label.textContent =
                            'Logging Out...';
                    }
                }
            );
        }
    }
);
</script>
