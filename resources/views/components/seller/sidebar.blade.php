{{-- =========================================================
     SELLER SIDEBAR
     resources/views/components/seller/sidebar.blade.php
     
     Admin-matched styling + preserved Seller UX
========================================================= --}}

<aside
    id="sellerSidebar"
    class="
        fixed left-0 top-0 z-50

        w-72 h-screen

        overflow-hidden

        bg-gradient-to-b
        from-maroon-900
        to-maroon-950

        rounded-tr-[4rem]

        flex flex-col

        py-8 px-5

        text-white

        transition-all duration-300

        sidebar-reload
    "
>

    <div class="flex-1 min-h-0">

        <!-- =====================================================
             LOGO
        ====================================================== -->

        <div
            id="seller-sidebar-logo"
            class="
                flex
                items-center
                justify-center

                mb-10
                px-2
                -mt-4

                transition-all
                duration-300

                sidebar-logo-reload
            "
        >

            <a
                href="{{ route('seller.dashboard') }}"
                aria-label="ShopEase Seller Dashboard"
                class="
                    flex
                    items-center
                    justify-center
                "
            >

                <img
                    src="{{ asset('icons/seller/sidebar&navbar/shopease.png') }}"
                    alt="ShopEase"

                    class="
                        seller-sidebar-logo-image
                        h-20
                        w-auto
                        object-contain
                    "
                >

            </a>

        </div>



        <!-- =====================================================
             NAVIGATION
        ====================================================== -->

        <nav
            id="seller-sidebar-nav"
            class="space-y-1"
        >


            {{-- =================================================
                 DASHBOARD
            ================================================== --}}

            <a
                href="{{ route('seller.dashboard') }}"

                class="
                    sidebar-link
                    sidebar-menu-item

                    seller-nav-item

                    flex
                    items-center

                    gap-3
                    px-4
                    py-3

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

                        flex
                        items-center
                        justify-center

                        shrink-0

                        w-5
                        h-5
                    "
                >

                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/dashboard.png') }}"

                        class="
                            sidebar-icon

                            w-5
                            h-5

                            object-contain
                            shrink-0
                        "

                        alt=""
                    >

                </span>


                <span
                    class="
                        sidebar-label

                        text-[16px]
                        font-medium

                        whitespace-nowrap
                    "
                >
                    Dashboard
                </span>

            </a>



            {{-- =================================================
                 INVENTORY
            ================================================== --}}

            <a
                href="#"
                onclick="return false;"

                class="
                    sidebar-link
                    sidebar-menu-item

                    seller-nav-item

                    flex
                    items-center

                    gap-3
                    px-4
                    py-3

                    rounded-full

                    transition-all
                    duration-300

                    w-full

                    hover:bg-maroon-800/50
                    hover:translate-x-1
                "
            >

                <span
                    class="
                        sidebar-icon-wrapper

                        flex
                        items-center
                        justify-center

                        shrink-0

                        w-5
                        h-5
                    "
                >

                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/inventory.png') }}"

                        class="
                            sidebar-icon

                            w-5
                            h-5

                            object-contain
                            shrink-0
                        "

                        alt=""
                    >

                </span>


                <span
                    class="
                        sidebar-label

                        text-[16px]
                        font-medium

                        whitespace-nowrap
                    "
                >
                    Inventory
                </span>

            </a>



            {{-- =================================================
                 ORDER MANAGEMENT LABEL
            ================================================== --}}

            <div
                id="seller-order-management-label"

                class="
                    sidebar-section-label

                    mt-4
                    mb-1
                    px-1

                    text-[16px]
                    font-normal

                    text-white/70

                    whitespace-nowrap
                "
            >
                Order Management
            </div>



            {{-- =================================================
                 ORDERS ACTIVE STATE
            ================================================== --}}

            @php

                $ordersActive =
                    request()->routeIs('seller.orders.*')
                    ||
                    request()->routeIs('seller.order.*')
                    ||
                    request()->routeIs('seller.shipping.*')
                    ||
                    request()->routeIs('seller.feedback.*');

            @endphp



            {{-- =================================================
                 ORDERS PARENT
            ================================================== --}}

            <button
                type="button"

                id="sellerOrdersToggle"

                class="
                    sidebar-link
                    sidebar-menu-item

                    seller-nav-item
                    seller-orders-toggle

                    flex
                    items-center

                    gap-3
                    px-4
                    py-3

                    rounded-full

                    transition-all
                    duration-300

                    w-full

                    text-left

                    border-none
                    cursor-pointer

                    bg-transparent
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

                        flex
                        items-center
                        justify-center

                        shrink-0

                        w-5
                        h-5
                    "
                >

                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/orders.png') }}"

                        class="
                            sidebar-icon

                            w-5
                            h-5

                            object-contain
                            shrink-0
                        "

                        alt=""
                    >

                </span>


                <span
                    class="
                        sidebar-label

                        text-[16px]
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

                        flex
                        items-center
                        justify-center

                        shrink-0

                        w-5
                        h-5

                        transition-transform
                        duration-300
                    "
                >

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"

                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"

                        class="w-4 h-4"
                    >

                        <path
                            d="M6 9l6 6 6-6"
                        />

                    </svg>

                </span>

            </button>



            {{-- =================================================
                 ORDERS SUBMENU
            ================================================== --}}

            <div
                id="sellerOrdersSubmenu"

                class="
                    seller-orders-submenu

                    overflow-hidden

                    transition-all
                    duration-300

                    {{
                        $ordersActive
                            ? 'open max-h-[180px] opacity-100'
                            : 'max-h-0 opacity-0'
                    }}
                "
            >

                <!-- ORDER STATUS -->

                <a
                    href="#"
                    onclick="return false;"

                    class="
                        seller-subnav-item

                        flex
                        items-center

                        min-h-[42px]

                        ml-9
                        px-4

                        rounded-full

                        text-[15px]
                        font-normal

                        text-white/95

                        transition-all
                        duration-300

                        hover:bg-maroon-800/50
                        hover:translate-x-1

                        {{
                            request()->routeIs('seller.order.status.*')
                                ? 'bg-maroon-800/70 font-medium'
                                : ''
                        }}
                    "
                >
                    Order Status
                </a>



                <!-- SHIPPING STATUS -->

                <a
                    href="#"
                    onclick="return false;"

                    class="
                        seller-subnav-item

                        flex
                        items-center

                        min-h-[42px]

                        ml-9
                        px-4

                        rounded-full

                        text-[15px]
                        font-normal

                        text-white/95

                        transition-all
                        duration-300

                        hover:bg-maroon-800/50
                        hover:translate-x-1

                        {{
                            request()->routeIs('seller.shipping.*')
                                ? 'bg-maroon-800/70 font-medium'
                                : ''
                        }}
                    "
                >
                    Shipping Status
                </a>



                <!-- CUSTOMER FEEDBACK -->

                <a
                    href="#"
                    onclick="return false;"

                    class="
                        seller-subnav-item

                        flex
                        items-center

                        min-h-[42px]

                        ml-9
                        px-4

                        rounded-full

                        text-[15px]
                        font-normal

                        text-white/95

                        transition-all
                        duration-300

                        hover:bg-maroon-800/50
                        hover:translate-x-1

                        {{
                            request()->routeIs('seller.feedback.*')
                                ? 'bg-maroon-800/70 font-medium'
                                : ''
                        }}
                    "
                >
                    Customer Feedback
                </a>

            </div>



            {{-- =================================================
                 REPORTS
            ================================================== --}}

            <a
                href="#"
                onclick="return false;"

                class="
                    sidebar-link
                    sidebar-menu-item

                    seller-nav-item

                    flex
                    items-center

                    gap-3
                    px-4
                    py-3

                    rounded-full

                    transition-all
                    duration-300

                    w-full

                    hover:bg-maroon-800/50
                    hover:translate-x-1
                "
            >

                <span
                    class="
                        sidebar-icon-wrapper

                        flex
                        items-center
                        justify-center

                        shrink-0

                        w-5
                        h-5
                    "
                >

                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/reports.png') }}"

                        class="
                            sidebar-icon

                            w-5
                            h-5

                            object-contain
                            shrink-0
                        "

                        alt=""
                    >

                </span>


                <span
                    class="
                        sidebar-label

                        text-[16px]
                        font-medium

                        whitespace-nowrap
                    "
                >
                    Reports
                </span>

            </a>



            {{-- =================================================
                 MESSAGES
            ================================================== --}}

            <a
                href="#"
                onclick="return false;"

                class="
                    sidebar-link
                    sidebar-menu-item

                    seller-nav-item

                    flex
                    items-center

                    gap-3
                    px-4
                    py-3

                    rounded-full

                    transition-all
                    duration-300

                    w-full

                    hover:bg-maroon-800/50
                    hover:translate-x-1
                "
            >

                <span
                    class="
                        sidebar-icon-wrapper

                        flex
                        items-center
                        justify-center

                        shrink-0

                        w-5
                        h-5
                    "
                >

                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/messages.png') }}"

                        class="
                            sidebar-icon

                            w-5
                            h-5

                            object-contain
                            shrink-0
                        "

                        alt=""
                    >

                </span>


                <span
                    class="
                        sidebar-label

                        text-[16px]
                        font-medium

                        whitespace-nowrap
                    "
                >
                    Messages
                </span>

            </a>



            {{-- =================================================
                 ACCOUNT
            ================================================== --}}

            <a
                href="#"
                onclick="return false;"

                class="
                    sidebar-link
                    sidebar-menu-item

                    seller-nav-item

                    flex
                    items-center

                    gap-3
                    px-4
                    py-3

                    rounded-full

                    transition-all
                    duration-300

                    w-full

                    hover:bg-maroon-800/50
                    hover:translate-x-1
                "
            >

                <span
                    class="
                        sidebar-icon-wrapper

                        flex
                        items-center
                        justify-center

                        shrink-0

                        w-5
                        h-5
                    "
                >

                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/account-management.png') }}"

                        class="
                            sidebar-icon

                            w-5
                            h-5

                            object-contain
                            shrink-0
                        "

                        alt=""
                    >

                </span>


                <span
                    class="
                        sidebar-label

                        text-[16px]
                        font-medium

                        whitespace-nowrap
                    "
                >
                    Account
                </span>

            </a>

        </nav>

    </div>



    <!-- =====================================================
         LOGOUT
    ====================================================== -->

    <form
        action="{{ route('logout') }}"
        method="POST"

        class="
            w-full
            shrink-0
        "
    >

        @csrf

        <button
            type="submit"

            class="
                sidebar-logout
                sidebar-logout-reload

                flex
                items-center
                justify-center

                gap-2

                border
                border-white/40

                rounded-full

                py-3

                text-[15px]
                font-medium

                hover:bg-white/10

                transition-all
                duration-300

                w-full

                cursor-pointer
            "
        >

            <span
                class="
                    sidebar-icon-wrapper

                    flex
                    items-center
                    justify-center

                    shrink-0

                    w-5
                    h-5
                "
            >

                <img
                    src="{{ asset('icons/seller/sidebar&navbar/log-out.png') }}"

                    class="
                        sidebar-icon

                        w-4
                        h-4

                        object-contain
                        shrink-0
                    "

                    alt=""
                >

            </span>


            <span
                class="sidebar-label"
            >
                Log Out
            </span>

        </button>

    </form>

</aside>



<style>

/* =========================================================
   SELLER SIDEBAR BASE
========================================================= */

#sellerSidebar {

    width: 288px;
    height: 100vh;

    position: fixed;

    top: 0;
    left: 0;

    z-index: 50;

    overflow: hidden;

    display: flex;
    flex-direction: column;

    padding:
        32px
        20px;

    box-sizing: border-box;

    color: #FFFFFF;


    /*
     * REVERSED GRADIENT
     *
     * LIGHTER ONLY AT THE TOP
     * DARKER + MORE DOMINANT AT THE BOTTOM
     */
    background:
        linear-gradient(
            to bottom,
            #5B1414 0%,
            #501111 30%,
            #410E0E 100%
        );


    border-top-right-radius:
        4rem;


    transition:
        width 0.3s ease,
        transform 0.25s ease;

}


/* =========================================================
   NAV ITEM
========================================================= */

#sellerSidebar .sidebar-link {

    transform:
        translateX(0)
        scale(1);

    transform-origin:
        center;

    will-change:
        transform,
        background-color,
        box-shadow;


    transition:
        background-color
        0.25s ease,

        transform
        0.22s cubic-bezier(
            0.22,
            1,
            0.36,
            1
        ),

        box-shadow
        0.25s ease,

        width
        0.30s ease,

        height
        0.30s ease,

        padding
        0.30s ease,

        margin
        0.30s ease;

}


/* =========================================================
   NORMAL HOVER
========================================================= */

#sellerSidebar .sidebar-link:hover {

    background:
        rgba(
            255,
            255,
            255,
            0.08
        );

    transform:
        translateX(4px);

}


/* =========================================================
   ACTIVE
========================================================= */

#sellerSidebar
.sidebar-link.bg-maroon-700\/60 {

    background:
        rgba(
            165,
            42,
            42,
            0.72
        );

    box-shadow:
        0 4px 12px
        rgba(
            46,
            0,
            0,
            0.20
        );

}


/* =========================================================
   ACTIVE HOVER
========================================================= */

#sellerSidebar
.sidebar-link.bg-maroon-700\/60:hover {

    background:
        rgba(
            165,
            42,
            42,
            0.82
        );

    transform:
        translateX(0);

}


/* =========================================================
   ICON
========================================================= */

#sellerSidebar .sidebar-icon-wrapper {

    width:
        20px;

    height:
        20px;

    flex:
        0 0 20px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

}


#sellerSidebar .sidebar-icon {

    width:
        20px;

    height:
        20px;

    object-fit:
        contain;

    display:
        block;

}


/* =========================================================
   LOGOUT ICON
========================================================= */

#sellerSidebar .sidebar-logout
.sidebar-icon {

    width:
        16px;

    height:
        16px;

}


/* =========================================================
   ORDERS CHEVRON
========================================================= */

.seller-orders-chevron {

    transform:
        rotate(0deg);

}


.seller-orders-chevron.open {

    transform:
        rotate(180deg);

}


/* =========================================================
   ORDERS SUBMENU
========================================================= */

.seller-orders-submenu {

    max-height:
        0;

    opacity:
        0;

    overflow:
        hidden;

    transition:
        max-height
        0.3s ease,

        opacity
        0.25s ease;

}


.seller-orders-submenu.open {

    max-height:
        180px;

    opacity:
        1;

}


/* =========================================================
   COLLAPSED SIDEBAR
========================================================= */

#sellerSidebar.seller-sidebar-collapsed {

    width:
        80px;

}


/* =========================================================
   COLLAPSED LOGO
========================================================= */

#sellerSidebar.seller-sidebar-collapsed
#seller-sidebar-logo {

    height:
        80px;

    min-height:
        80px;

    margin-top:
        -16px;

    margin-bottom:
        24px;

    padding:
        0;

}


#sellerSidebar.seller-sidebar-collapsed
#seller-sidebar-logo img {

    display:
        none;

}


/* =========================================================
   COLLAPSED LABELS
========================================================= */

#sellerSidebar.seller-sidebar-collapsed
.sidebar-label {

    display:
        none;

}


#sellerSidebar.seller-sidebar-collapsed
.sidebar-section-label {

    display:
        none;

}


/* =========================================================
   COLLAPSED MENU ITEM
========================================================= */

#sellerSidebar.seller-sidebar-collapsed
.sidebar-link {

    width:
        40px;

    height:
        40px;

    min-height:
        40px;

    margin-left:
        auto;

    margin-right:
        auto;

    padding:
        0;

    gap:
        0;

    justify-content:
        center;

    border-radius:
        999px;

}


/* =========================================================
   COLLAPSED HOVER
========================================================= */

#sellerSidebar.seller-sidebar-collapsed
.sidebar-link:hover {

    background:
        rgba(
            255,
            255,
            255,
            0.10
        );

    transform:
        scale(1.04);

}


/* =========================================================
   COLLAPSED ACTIVE
========================================================= */

#sellerSidebar.seller-sidebar-collapsed
.sidebar-link.bg-maroon-700\/60 {

    background:
        #A52A2A;

    box-shadow:
        0 4px 12px
        rgba(
            46,
            0,
            0,
            0.25
        );

}


/* =========================================================
   COLLAPSED ACTIVE HOVER
========================================================= */

#sellerSidebar.seller-sidebar-collapsed
.sidebar-link.bg-maroon-700\/60:hover {

    background:
        #B52D2D;

    transform:
        scale(1.04);

}


/* =========================================================
   COLLAPSED ICON
========================================================= */

#sellerSidebar.seller-sidebar-collapsed
.sidebar-icon-wrapper {

    width:
        20px;

    height:
        20px;

    flex:
        0 0 20px;

}


#sellerSidebar.seller-sidebar-collapsed
.sidebar-icon {

    width:
        20px;

    height:
        20px;

}


/* =========================================================
   COLLAPSED ORDERS SUBMENU
========================================================= */

#sellerSidebar.seller-sidebar-collapsed
.seller-orders-submenu {

    display:
        none !important;

}


/* =========================================================
   COLLAPSED CHEVRON
========================================================= */

#sellerSidebar.seller-sidebar-collapsed
.seller-orders-chevron {

    display:
        none;

}


/* =========================================================
   LOGOUT
========================================================= */

#sellerSidebar .sidebar-logout {

    min-height:
        48px;

    border-radius:
        999px;

    transition:
        background-color
        0.25s ease,

        transform
        0.2s ease,

        width
        0.3s ease,

        height
        0.3s ease,

        padding
        0.3s ease,

        margin
        0.3s ease;

}


#sellerSidebar .sidebar-logout:hover {

    background:
        rgba(
            255,
            255,
            255,
            0.10
        );

    transform:
        translateY(-1px);

}


/* =========================================================
   COLLAPSED LOGOUT
========================================================= */

#sellerSidebar.seller-sidebar-collapsed
.sidebar-logout {

    width:
        40px;

    height:
        40px;

    min-height:
        40px;

    padding:
        0;

    margin-left:
        auto;

    margin-right:
        auto;

    gap:
        0;

    justify-content:
        center;

    border-radius:
        999px;

}


/* =========================================================
   COLLAPSED LOGOUT HOVER
========================================================= */

#sellerSidebar.seller-sidebar-collapsed
.sidebar-logout:hover {

    background:
        rgba(
            255,
            255,
            255,
            0.12
        );

    transform:
        scale(1.04);

}


/* =========================================================
   RELOAD ANIMATION
========================================================= */

@keyframes sellerSidebarMenuReload {

    from {

        opacity:
            0;

        transform:
            translateX(-20px);

    }

    to {

        opacity:
            1;

        transform:
            translateX(0);

    }

}


.sidebar-menu-item {

    opacity:
        0;

    animation-name:
        sellerSidebarMenuReload;

    animation-duration:
        0.35s;

    animation-timing-function:
        ease-out;

    animation-fill-mode:
        both;

}


/* =========================================================
   TOP TO BOTTOM DELAYS
========================================================= */

.sidebar-menu-item:nth-child(1) {
    animation-delay:
        0.05s;
}

.sidebar-menu-item:nth-child(2) {
    animation-delay:
        0.10s;
}

.sidebar-menu-item:nth-child(3) {
    animation-delay:
        0.15s;
}

.sidebar-menu-item:nth-child(4) {
    animation-delay:
        0.20s;
}

.sidebar-menu-item:nth-child(5) {
    animation-delay:
        0.25s;
}

.sidebar-menu-item:nth-child(6) {
    animation-delay:
        0.30s;
}

.sidebar-menu-item:nth-child(7) {
    animation-delay:
        0.35s;
}

.sidebar-menu-item:nth-child(8) {
    animation-delay:
        0.40s;
}

.sidebar-menu-item:nth-child(9) {
    animation-delay:
        0.45s;
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 760px) {

    #sellerSidebar {

        width:
            288px;

        transform:
            translateX(-100%);

        transition:
            transform
            0.25s ease;

        box-shadow:
            10px 0 30px
            rgba(
                0,
                0,
                0,
                0.18
            );

    }


    #sellerSidebar.mobile-open {

        transform:
            translateX(0);

    }


    #sellerSidebar.seller-sidebar-collapsed {

        width:
            288px;

    }

}

</style>



<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /* =====================================================
           ELEMENTS
        ====================================================== */

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


        const sidebarLogo =
            document.getElementById(
                'seller-sidebar-logo'
            );



        /* =====================================================
           SIDEBAR STATE
        ====================================================== */

        let collapsed = false;



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


                    /*
                    |------------------------------------------------------------------
                    | Do not open submenu while collapsed
                    |------------------------------------------------------------------
                    */

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


                    if (ordersChevron) {

                        ordersChevron.classList.toggle(
                            'open'
                        );

                    }


                    ordersToggle.setAttribute(
                        'aria-expanded',
                        String(!isOpen)
                    );


                    /*
                    |------------------------------------------------------------------
                    | Remember state
                    |------------------------------------------------------------------
                    */

                    ordersToggle.dataset.wasOpen =
                        String(!isOpen);

                }
            );

        }



        /* =====================================================
           COLLAPSE SIDEBAR
        ====================================================== */

        function collapseSidebar() {

            if (!sidebar) {
                return;
            }


            collapsed = true;


            /*
            |------------------------------------------------------------------
            | Remember Orders state
            |------------------------------------------------------------------
            */

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


            /*
            |------------------------------------------------------------------
            | ONLY CHANGE COLLAPSED STATE
            |------------------------------------------------------------------
            |
            | All normal hover, radius,
            | spacing, and visual styling
            | remain defined by CSS.
            |
            */

            sidebar.classList.add(
                'seller-sidebar-collapsed'
            );


            /*
            |------------------------------------------------------------------
            | Temporarily close Orders submenu
            |------------------------------------------------------------------
            */

            if (ordersSubmenu) {

                ordersSubmenu.classList.remove(
                    'open'
                );

            }


            if (ordersChevron) {

                ordersChevron.classList.remove(
                    'open'
                );

            }


            if (ordersToggle) {

                ordersToggle.setAttribute(
                    'aria-expanded',
                    'false'
                );

            }

        }



        /* =====================================================
           EXPAND SIDEBAR
        ====================================================== */

        function expandSidebar() {

            if (!sidebar) {
                return;
            }


            collapsed = false;


            /*
            |------------------------------------------------------------------
            | Remove ONLY collapsed state
            |------------------------------------------------------------------
            */

            sidebar.classList.remove(
                'seller-sidebar-collapsed'
            );


            /*
            |------------------------------------------------------------------
            | Restore Orders submenu
            |------------------------------------------------------------------
            */

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


                if (ordersChevron) {

                    ordersChevron.classList.add(
                        'open'
                    );

                }


                ordersToggle.setAttribute(
                    'aria-expanded',
                    'true'
                );

            }

        }



        /* =====================================================
           TOGGLE SIDEBAR
        ====================================================== */

        function toggleSidebar() {

            if (!sidebar) {
                return;
            }


            /*
            |------------------------------------------------------------------
            | MOBILE
            |------------------------------------------------------------------
            */

            if (
                window.innerWidth <= 760
            ) {

                sidebar.classList.toggle(
                    'mobile-open'
                );

                return;

            }


            /*
            |------------------------------------------------------------------
            | DESKTOP
            |------------------------------------------------------------------
            */

            if (collapsed) {

                expandSidebar();

            } else {

                collapseSidebar();

            }

        }



        /* =====================================================
           NAVBAR EVENT
        ====================================================== */

        document.body.addEventListener(
            'toggle-seller-sidebar',
            function () {

                toggleSidebar();

            }
        );



        /* =====================================================
           GLOBAL HELPER
        ====================================================== */

        window.toggleSellerSidebar =
            function () {

                toggleSidebar();

            };



        /* =====================================================
           ESCAPE KEY
        ====================================================== */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key !== 'Escape'
                ) {

                    return;

                }


                /*
                |------------------------------------------------------------------
                | Close mobile sidebar
                |------------------------------------------------------------------
                */

                if (
                    sidebar &&
                    sidebar.classList.contains(
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
           RELOAD UX
        ====================================================== */

        if (sidebar) {


            /*
            |------------------------------------------------------------------
            | Logo
            |------------------------------------------------------------------
            */

            if (sidebarLogo) {

                sidebarLogo.classList.remove(
                    'sidebar-logo-reload'
                );

                void sidebarLogo.offsetWidth;

                sidebarLogo.classList.add(
                    'sidebar-logo-reload'
                );

            }


            /*
            |------------------------------------------------------------------
            | Menu items
            |------------------------------------------------------------------
            */

            const menuItems =
                document.querySelectorAll(
                    '#sellerSidebar .sidebar-menu-item'
                );


            menuItems.forEach(
                function (item) {

                    item.classList.remove(
                        'sidebar-menu-item'
                    );

                    void item.offsetWidth;

                    item.classList.add(
                        'sidebar-menu-item'
                    );

                }
            );


            /*
            |------------------------------------------------------------------
            | Logout
            |------------------------------------------------------------------
            */

            const logout =
                document.querySelector(
                    '#sellerSidebar .sidebar-logout-reload'
                );


            if (logout) {

                logout.classList.remove(
                    'sidebar-logout-reload'
                );

                void logout.offsetWidth;

                logout.classList.add(
                    'sidebar-logout-reload'
                );

            }

        }



        /* =====================================================
           INITIAL ORDERS STATE
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

    }

);

</script>