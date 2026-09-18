{{-- =========================================================
     SELLER SIDEBAR
     resources/views/components/seller/sidebar.blade.php

     ADMIN-MATCHED VISUAL / UX
     ---------------------------------------------------------
     - Same expanded width as Admin: 256px
     - Same collapsed width: 80px
     - Same gradient / radius
     - Same logo size and spacing
     - Same 14px labels
     - Same 18px icons
     - Same link padding and gaps
     - Same active / hover colors
     - Same 4px hover movement
     - Same reload animation timing
     - Same logout sizing / animation
     - Seller Orders accordion preserved
========================================================= --}}

@php
    $ordersActive =
        request()->routeIs('seller.orders.*')
        || request()->routeIs('seller.order.*')
        || request()->routeIs('seller.shipping.*')
        || request()->routeIs('seller.feedback.*');
@endphp


<aside
    id="sellerSidebar"
    class="
        fixed left-0 top-0 z-50
        w-72 h-screen
        overflow-hidden
        bg-gradient-to-b from-maroon-900 to-maroon-950
        rounded-tr-[4rem]
        flex flex-col
        py-6 px-4
        text-white
        transition-all duration-300
        sidebar-reload
    "
>
    <div class="flex-1 min-h-0">

        {{-- =====================================================
             LOGO
        ====================================================== --}}
        <div
            id="seller-sidebar-logo"
            class="
                flex items-center justify-center
                mb-7 px-1 -mt-2
                transition-all duration-500
                sidebar-logo-reload
            "
        >
            <a
                href="{{ route('seller.dashboard') }}"
                aria-label="ShopEase Seller Dashboard"
                class="flex items-center justify-center"
            >
                <img
                    src="{{ asset('icons/seller/sidebar&navbar/shopease.png') }}"
                    alt="ShopEase"
                    class="h-16 w-auto object-contain"
                >
            </a>
        </div>


        {{-- =====================================================
             NAVIGATION
        ====================================================== --}}
        <nav
            id="seller-sidebar-nav"
            class="space-y-0.5"
        >

            {{-- DASHBOARD --}}
            <a
                href="{{ route('seller.dashboard') }}"
                class="
                    sidebar-link
                    sidebar-menu-item
                    seller-menu-delay-1
                    flex items-center
                    gap-2.5
                    px-3
                    py-2.5
                    rounded-full
                    transition-all
                    duration-300
                    w-full
                    {{
                        request()->routeIs('seller.dashboard')
                            ? 'bg-maroon-700/60'
                            : 'hover:bg-maroon-800/50 hover:translate-x-1'
                    }}
                "
            >
                <span
                    class="
                        sidebar-icon-wrapper
                        flex items-center justify-center
                        shrink-0
                        w-[18px] h-[18px]
                        transition-all duration-300
                    "
                >
                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/dashboard.png') }}"
                        alt=""
                        class="
                            sidebar-icon
                            w-[18px] h-[18px]
                            object-contain
                            shrink-0
                        "
                    >
                </span>

                <span
                    class="
                        sidebar-label
                        text-[14px]
                        font-medium
                        whitespace-nowrap
                    "
                >
                    Dashboard
                </span>
            </a>


            {{-- INVENTORY --}}
            <a
                href="{{ route('seller.inventory') }}"
                class="
                    sidebar-link
                    sidebar-menu-item
                    seller-menu-delay-2
                    flex items-center
                    gap-2.5
                    px-3
                    py-2.5
                    rounded-full
                    transition-all
                    duration-300
                    w-full
                    {{
                        request()->routeIs('seller.inventory')
                            ? 'bg-maroon-700/60'
                            : 'hover:bg-maroon-800/50 hover:translate-x-1'
                    }}
                "
            >
                <span
                    class="
                        sidebar-icon-wrapper
                        flex items-center justify-center
                        shrink-0
                        w-[18px] h-[18px]
                        transition-all duration-300
                    "
                >
                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/inventory.png') }}"
                        alt=""
                        class="
                            sidebar-icon
                            w-[18px] h-[18px]
                            object-contain
                            shrink-0
                        "
                    >
                </span>

                <span
                    class="
                        sidebar-label
                        text-[14px]
                        font-medium
                        whitespace-nowrap
                    "
                >
                    Inventory
                </span>
            </a>


            {{-- ORDER MANAGEMENT LABEL --}}
            <div
                id="seller-order-management-label"
                class="
                    sidebar-section-label
                    mt-3 mb-1
                    px-3
                    text-[11px]
                    font-normal
                    text-white/60
                    whitespace-nowrap
                "
            >
                Order Management
            </div>


            {{-- ORDERS --}}
            <button
                type="button"
                id="sellerOrdersToggle"
                class="
                    sidebar-link
                    sidebar-menu-item
                    seller-menu-delay-3
                    flex items-center
                    gap-2.5
                    px-3
                    py-2.5
                    rounded-full
                    transition-all
                    duration-300
                    w-full
                    text-left
                    border-none
                    cursor-pointer
                    text-white
                    {{
                        $ordersActive
                            ? 'bg-maroon-700/60'
                            : 'hover:bg-maroon-800/50 hover:translate-x-1'
                    }}
                "
                aria-expanded="{{ $ordersActive ? 'true' : 'false' }}"
                aria-controls="sellerOrdersSubmenu"
            >
                <span
                    class="
                        sidebar-icon-wrapper
                        flex items-center justify-center
                        shrink-0
                        w-[18px] h-[18px]
                        transition-all duration-300
                    "
                >
                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/orders.png') }}"
                        alt=""
                        class="
                            sidebar-icon
                            w-[18px] h-[18px]
                            object-contain
                            shrink-0
                        "
                    >
                </span>

                <span
                    class="
                        sidebar-label
                        text-[14px]
                        font-medium
                        whitespace-nowrap
                        flex-1
                    "
                >
                    Orders
                </span>

                <span
                    id="sellerOrdersChevron"
                    class="
                        seller-orders-chevron
                        flex items-center justify-center
                        shrink-0
                        w-[18px] h-[18px]
                        transition-transform
                        duration-300
                        {{ $ordersActive ? 'open' : '' }}
                    "
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="w-3.5 h-3.5"
                        aria-hidden="true"
                    >
                        <path d="M6 9l6 6 6-6"/>
                    </svg>
                </span>
            </button>


            {{-- ORDERS SUBMENU --}}
            <div
                id="sellerOrdersSubmenu"
                class="
                    seller-orders-submenu
                    overflow-hidden
                    {{ $ordersActive ? 'open' : '' }}
                "
            >
                <a
                    href="{{ route('seller.order.status') }}"
                    class="
                        seller-subnav-item
                        flex items-center
                        min-h-[36px]
                        ml-8
                        px-3
                        rounded-full
                        text-[13px]
                        font-normal
                        text-white/90
                        transition-all
                        duration-300
                        {{
                            request()->routeIs('seller.order.status')
                                ? 'bg-maroon-800/70 font-medium'
                                : 'hover:bg-maroon-800/50 hover:translate-x-1'
                        }}
                    "
                >
                    Order Status
                </a>

                <a
                    href="{{ route('seller.shipping.status') }}"
                    class="
                        seller-subnav-item
                        flex items-center
                        min-h-[36px]
                        ml-8
                        px-3
                        rounded-full
                        text-[13px]
                        font-normal
                        text-white/90
                        transition-all
                        duration-300
                        {{
                            request()->routeIs('seller.shipping.status')
                                ? 'bg-maroon-800/70 font-medium'
                                : 'hover:bg-maroon-800/50 hover:translate-x-1'
                        }}
                    "
                >
                    Shipping Status
                </a>

                <a
                    href="#"
                    onclick="return false;"
                    class="
                        seller-subnav-item
                        flex items-center
                        min-h-[36px]
                        ml-8
                        px-3
                        rounded-full
                        text-[13px]
                        font-normal
                        text-white/90
                        transition-all
                        duration-300
                        {{
                            request()->routeIs('seller.feedback.*')
                                ? 'bg-maroon-800/70 font-medium'
                                : 'hover:bg-maroon-800/50 hover:translate-x-1'
                        }}
                    "
                >
                    Customer Feedback
                </a>
            </div>


            {{-- REPORTS --}}
            <a
                href="#"
                onclick="return false;"
                class="
                    sidebar-link
                    sidebar-menu-item
                    seller-menu-delay-4
                    flex items-center
                    gap-2.5
                    px-3
                    py-2.5
                    rounded-full
                    transition-all
                    duration-300
                    w-full
                    opacity-100
                    cursor-default
                "
            >
                <span
                    class="
                        sidebar-icon-wrapper
                        flex items-center justify-center
                        shrink-0
                        w-[18px] h-[18px]
                    "
                >
                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/reports.png') }}"
                        alt=""
                        class="
                            sidebar-icon
                            w-[18px] h-[18px]
                            object-contain
                            shrink-0
                        "
                    >
                </span>

                <span
                    class="
                        sidebar-label
                        text-[14px]
                        font-medium
                        whitespace-nowrap
                    "
                >
                    Reports
                </span>
            </a>


            {{-- MESSAGES --}}
            <a
                href="#"
                onclick="return false;"
                class="
                    sidebar-link
                    sidebar-menu-item
                    seller-menu-delay-5
                    flex items-center
                    gap-2.5
                    px-3
                    py-2.5
                    rounded-full
                    transition-all
                    duration-300
                    w-full
                    opacity-100
                    cursor-default
                "
            >
                <span
                    class="
                        sidebar-icon-wrapper
                        flex items-center justify-center
                        shrink-0
                        w-[18px] h-[18px]
                    "
                >
                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/messages.png') }}"
                        alt=""
                        class="
                            sidebar-icon
                            w-[18px] h-[18px]
                            object-contain
                            shrink-0
                        "
                    >
                </span>

                <span
                    class="
                        sidebar-label
                        text-[14px]
                        font-medium
                        whitespace-nowrap
                    "
                >
                    Messages
                </span>
            </a>


            {{-- ACCOUNT --}}
            <a
                href="#"
                onclick="return false;"
                class="
                    sidebar-link
                    sidebar-menu-item
                    seller-menu-delay-6
                    flex items-center
                    gap-2.5
                    px-3
                    py-2.5
                    rounded-full
                    transition-all
                    duration-300
                    w-full
                    opacity-100
                    cursor-default
                "
            >
                <span
                    class="
                        sidebar-icon-wrapper
                        flex items-center justify-center
                        shrink-0
                        w-[18px] h-[18px]
                    "
                >
                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/account-management.png') }}"
                        alt=""
                        class="
                            sidebar-icon
                            w-[18px] h-[18px]
                            object-contain
                            shrink-0
                        "
                    >
                </span>

                <span
                    class="
                        sidebar-label
                        text-[14px]
                        font-medium
                        whitespace-nowrap
                    "
                >
                    Account
                </span>
            </a>

        </nav>
    </div>


    {{-- =====================================================
         LOGOUT
    ====================================================== --}}
    <form
        action="{{ route('logout') }}"
        method="POST"
        class="w-full"
        id="sellerLogoutForm"
    >
        @csrf

        <button
            type="submit"
            class="
                sidebar-logout
                sidebar-logout-reload
                shrink-0
                flex items-center justify-center
                gap-2
                border border-white/40
                rounded-full
                py-2.5
                text-[14px]
                font-medium
                hover:bg-white/10
                transition-all duration-300
                w-full
                cursor-pointer
            "
        >
            <span
                class="
                    sidebar-icon-wrapper
                    flex items-center justify-center
                    shrink-0
                    w-[18px] h-[18px]
                "
            >
                <img
                    src="{{ asset('icons/seller/sidebar&navbar/log-out.png') }}"
                    alt=""
                    class="
                        sidebar-icon
                        w-[15px] h-[15px]
                        object-contain
                        shrink-0
                    "
                >
            </span>

            <span class="sidebar-label text-[14px]">
                Log Out
            </span>
        </button>
    </form>
</aside>


<style>
    /* =========================================================
       SELLER SIDEBAR
       MATCHES ADMIN SIDEBAR METRICS
    ========================================================== */

    /*
     * Admin sidebar starts as w-72 but is visually overridden
     * to 256px while expanded. Keep the same behavior here.
     */
    #sellerSidebar.sidebar-reload:not(.w-20) {
        width: 256px !important;
    }

    #sellerSidebar.w-20,
    #sellerSidebar.seller-sidebar-collapsed {
        width: 80px !important;
    }


    /* =========================================================
       ADMIN-MATCHED RELOAD UX
    ========================================================== */

    @keyframes sidebarMenuReload {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .sidebar-menu-item {
        opacity: 0;
        animation-name: sidebarMenuReload;
        animation-duration: 0.35s;
        animation-timing-function: ease-out;
        animation-fill-mode: both;
    }

    .seller-menu-delay-1 { animation-delay: 0.05s; }
    .seller-menu-delay-2 { animation-delay: 0.10s; }
    .seller-menu-delay-3 { animation-delay: 0.15s; }
    .seller-menu-delay-4 { animation-delay: 0.20s; }
    .seller-menu-delay-5 { animation-delay: 0.25s; }
    .seller-menu-delay-6 { animation-delay: 0.30s; }


    /* =========================================================
       LOGOUT ANIMATION
       Exact Admin timing
    ========================================================== */

    @keyframes sidebarLogoutReload {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .sidebar-logout-reload {
        opacity: 0;
        animation:
            sidebarLogoutReload
            0.45s
            ease-out
            0.6s
            forwards;
    }


    /* =========================================================
       ORDERS ACCORDION
    ========================================================== */

    .seller-orders-chevron {
        transform: rotate(0deg);
        transition: transform 0.3s ease;
    }

    .seller-orders-chevron.open {
        transform: rotate(180deg);
    }

    .seller-orders-submenu {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition:
            max-height 0.3s ease,
            opacity 0.3s ease,
            margin 0.3s ease;
    }

    .seller-orders-submenu.open {
        max-height: 140px;
        opacity: 1;
        margin-top: 2px;
        margin-bottom: 2px;
    }


    /* =========================================================
       COLLAPSED STATE
       Same 80px target size as Admin w-20 state
    ========================================================== */

    #sellerSidebar.seller-sidebar-collapsed
    #seller-sidebar-logo {
        min-height: 56px;
        margin-top: -8px;
        margin-bottom: 18px;
        padding: 0;
    }

    #sellerSidebar.seller-sidebar-collapsed
    #seller-sidebar-logo img {
        display: none;
    }

    #sellerSidebar.seller-sidebar-collapsed
    .sidebar-label,
    #sellerSidebar.seller-sidebar-collapsed
    .sidebar-section-label,
    #sellerSidebar.seller-sidebar-collapsed
    .seller-orders-chevron {
        display: none;
    }

    #sellerSidebar.seller-sidebar-collapsed
    .seller-orders-submenu {
        display: none !important;
    }

    #sellerSidebar.seller-sidebar-collapsed
    .sidebar-link {
        width: 40px;
        height: 40px;
        min-height: 40px;
        margin-left: auto;
        margin-right: auto;
        padding: 0;
        gap: 0;
        justify-content: center;
        border-radius: 999px;
    }

    #sellerSidebar.seller-sidebar-collapsed
    .sidebar-icon-wrapper {
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
    }

    #sellerSidebar.seller-sidebar-collapsed
    .sidebar-icon {
        width: 18px;
        height: 18px;
    }

    #sellerSidebar.seller-sidebar-collapsed
    .sidebar-logout {
        width: 40px;
        height: 40px;
        min-height: 40px;
        padding: 0;
        margin-left: auto;
        margin-right: auto;
        gap: 0;
        justify-content: center;
        border-radius: 999px;
    }

    #sellerSidebar.seller-sidebar-collapsed
    .sidebar-logout
    .sidebar-icon {
        width: 15px;
        height: 15px;
    }


    /* =========================================================
       MOBILE
    ========================================================== */

    @media (max-width: 760px) {
        #sellerSidebar {
            width: 256px !important;
            transform: translateX(-100%);
            transition:
                transform 0.25s ease,
                width 0.3s ease;
            box-shadow:
                10px 0 30px
                rgba(0, 0, 0, 0.18);
        }

        #sellerSidebar.mobile-open {
            transform: translateX(0);
        }

        #sellerSidebar.seller-sidebar-collapsed,
        #sellerSidebar.w-20 {
            width: 256px !important;
        }
    }


    /* =========================================================
       REDUCED MOTION
    ========================================================== */

    @media (prefers-reduced-motion: reduce) {
        #sellerSidebar,
        #sellerSidebar .sidebar-link,
        #sellerSidebar .seller-subnav-item,
        #sellerSidebar .sidebar-logout,
        .seller-orders-submenu,
        .seller-orders-chevron {
            transition: none !important;
        }

        .sidebar-menu-item,
        .sidebar-logout-reload {
            animation: none !important;
            opacity: 1;
            transform: none;
        }
    }
</style>


<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const sidebar =
            document.getElementById(
                'sellerSidebar'
            );

        const ordersToggle =
            document.getElementById(
                'sellerOrdersToggle'
            );

        const ordersSubmenu =
            document.getElementById(
                'sellerOrdersSubmenu'
            );

        const ordersChevron =
            document.getElementById(
                'sellerOrdersChevron'
            );

        const sellerLogoutForm =
            document.getElementById(
                'sellerLogoutForm'
            );


        /* =====================================================
           ORDERS INITIAL STATE
        ====================================================== */

        if (
            ordersToggle &&
            ordersSubmenu
        ) {
            const initialOpen =
                ordersSubmenu.classList.contains(
                    'open'
                );

            ordersToggle.dataset.wasOpen =
                initialOpen
                    ? 'true'
                    : 'false';
        }


        /* =====================================================
           ORDERS ACCORDION
        ====================================================== */

        if (
            ordersToggle &&
            ordersSubmenu
        ) {
            ordersToggle.addEventListener(
                'click',
                function () {

                    if (
                        sidebar &&
                        sidebar.classList.contains(
                            'seller-sidebar-collapsed'
                        )
                    ) {
                        return;
                    }

                    const isOpen =
                        ordersSubmenu.classList.contains(
                            'open'
                        );

                    ordersSubmenu.classList.toggle(
                        'open'
                    );

                    ordersChevron?.classList.toggle(
                        'open'
                    );

                    ordersToggle.setAttribute(
                        'aria-expanded',
                        String(!isOpen)
                    );

                    ordersToggle.dataset.wasOpen =
                        String(!isOpen);
                }
            );
        }


        /* =====================================================
           COLLAPSE / EXPAND
        ====================================================== */

        function collapseSellerSidebar() {
            if (!sidebar) {
                return;
            }

            if (
                ordersToggle &&
                ordersSubmenu
            ) {
                ordersToggle.dataset.wasOpen =
                    ordersSubmenu.classList.contains(
                        'open'
                    )
                        ? 'true'
                        : 'false';
            }

            sidebar.classList.remove(
                'w-72'
            );

            sidebar.classList.add(
                'w-20',
                'seller-sidebar-collapsed'
            );

            ordersSubmenu?.classList.remove(
                'open'
            );

            ordersChevron?.classList.remove(
                'open'
            );

            ordersToggle?.setAttribute(
                'aria-expanded',
                'false'
            );

            document.body.dispatchEvent(
                new CustomEvent(
                    'seller-sidebar-state-changed',
                    {
                        detail: {
                            collapsed: true
                        }
                    }
                )
            );
        }


        function expandSellerSidebar() {
            if (!sidebar) {
                return;
            }

            sidebar.classList.remove(
                'w-20',
                'seller-sidebar-collapsed'
            );

            sidebar.classList.add(
                'w-72'
            );

            const shouldReopenOrders =
                ordersToggle &&
                ordersToggle.dataset.wasOpen ===
                    'true';

            if (
                shouldReopenOrders &&
                ordersSubmenu
            ) {
                ordersSubmenu.classList.add(
                    'open'
                );

                ordersChevron?.classList.add(
                    'open'
                );

                ordersToggle?.setAttribute(
                    'aria-expanded',
                    'true'
                );
            }

            document.body.dispatchEvent(
                new CustomEvent(
                    'seller-sidebar-state-changed',
                    {
                        detail: {
                            collapsed: false
                        }
                    }
                )
            );
        }


        function toggleSellerSidebar() {
            if (!sidebar) {
                return;
            }

            if (
                window.innerWidth <= 760
            ) {
                sidebar.classList.toggle(
                    'mobile-open'
                );

                return;
            }

            if (
                sidebar.classList.contains(
                    'seller-sidebar-collapsed'
                )
            ) {
                expandSellerSidebar();
            } else {
                collapseSellerSidebar();
            }
        }


        /* =====================================================
           NAVBAR TOGGLE EVENT
        ====================================================== */

        document.body.addEventListener(
            'toggle-seller-sidebar',
            toggleSellerSidebar
        );

        window.toggleSellerSidebar =
            toggleSellerSidebar;


        /* =====================================================
           ESCAPE CLOSES MOBILE SIDEBAR
        ====================================================== */

        document.addEventListener(
            'keydown',
            function (event) {
                if (
                    event.key === 'Escape' &&
                    sidebar?.classList.contains(
                        'mobile-open'
                    )
                ) {
                    sidebar.classList.remove(
                        'mobile-open'
                    );
                }
            }
        );


        /* =====================================================
           LOGOUT UX
        ====================================================== */

        if (sellerLogoutForm) {
            sellerLogoutForm.addEventListener(
                'submit',
                function () {
                    const button =
                        sellerLogoutForm.querySelector(
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
                            '.sidebar-label'
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
