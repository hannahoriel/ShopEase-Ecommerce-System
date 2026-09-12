{{-- =========================================================
     SELLER NAVBAR
     resources/views/components/seller/navbar.blade.php

     Dynamic navbar position:
     - Burger + title follow the seller sidebar
     - Instant position adjustment
     - No delayed navbar animation
     - Sidebar remains responsible for its own animation
========================================================= --}}


<header
    id="seller-navbar"
    class="
        fixed
        top-0
        left-0
        right-0
        h-[104px]
        z-40
        bg-[#FFE6DE]
        flex
        items-center
        justify-between
    "
>


    <!-- =====================================================
         LEFT SIDE
    ====================================================== -->

    <div
        id="navbar-left"
        class="
            seller-navbar-left
            flex
            items-center
            gap-9
        "
    >


        <!-- =================================================
             SIDEBAR TOGGLE
        ================================================== -->

        <button
            id="sidebar-toggle"
            type="button"
            class="
                text-maroon-900
                flex
                items-center
                justify-center
                transition-transform
                duration-200
                hover:scale-110
                active:scale-95
                shrink-0
            "
            aria-label="Toggle seller sidebar"
            aria-controls="sellerSidebar"
            aria-expanded="true"
        >

            <svg
                class="w-7 h-7"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                />

            </svg>

        </button>


        <!-- =================================================
             PAGE TITLE
        ================================================== -->

        <h1
            class="
                text-[24px]
                font-bold
                text-gray-800
                leading-none
                whitespace-nowrap
            "
        >

            @yield(
                'page-title',
                'Dashboard'
            )

        </h1>

    </div>



    <!-- =====================================================
         RIGHT SIDE
    ====================================================== -->

    <div
        class="
            flex
            items-center
            gap-8
            mr-[42px]
        "
    >


        <!-- =================================================
             NOTIFICATIONS
        ================================================== -->

        <div
            class="relative"
        >

            <!-- NOTIFICATION BUTTON -->

            <button
                id="notification-button"
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
                aria-controls="notification-dropdown"
            >

                <img
                    src="{{ asset('icons/seller/sidebar&navbar/notification.png') }}"
                    class="
                        w-7
                        h-8
                        object-contain
                    "
                    alt="Notifications"
                >


                <span
                    class="
                        absolute
                        -top-2
                        -right-2
                        bg-red-600
                        text-white
                        text-[11px]
                        font-semibold
                        rounded-full
                        w-5
                        h-5
                        flex
                        items-center
                        justify-center
                    "
                >
                    4
                </span>

            </button>



            <!-- =================================================
                 NOTIFICATION DROPDOWN
            ================================================== -->

            <div
                id="notification-dropdown"
                class="
                    hidden
                    absolute
                    right-0
                    top-12
                    w-[320px]
                    bg-white
                    rounded-2xl
                    shadow-xl
                    border
                    border-gray-100
                    overflow-hidden
                    z-[60]
                "
            >


                <!-- HEADER -->

                <div
                    class="
                        px-5
                        py-4
                        border-b
                        border-gray-100
                        flex
                        items-center
                        justify-between
                    "
                >

                    <h3
                        class="
                            text-[16px]
                            font-bold
                            text-gray-800
                        "
                    >
                        Notifications
                    </h3>

                    <span
                        class="
                            text-xs
                            text-maroon-700
                            font-medium
                        "
                    >
                        4 new
                    </span>

                </div>



                <!-- NOTIFICATION LIST -->

                <div
                    class="
                        max-h-[300px]
                        overflow-y-auto
                    "
                >


                    <!-- ITEM 1 -->

                    <div
                        class="
                            px-5
                            py-4
                            hover:bg-gray-50
                            transition
                            cursor-pointer
                            border-b
                            border-gray-50
                        "
                    >

                        <p
                            class="
                                text-[14px]
                                font-semibold
                                text-gray-800
                            "
                        >
                            New Order Received
                        </p>

                        <p
                            class="
                                text-[12px]
                                text-gray-400
                                mt-1
                            "
                        >
                            Order #ORD-2089 has been placed.
                        </p>

                    </div>



                    <!-- ITEM 2 -->

                    <div
                        class="
                            px-5
                            py-4
                            hover:bg-gray-50
                            transition
                            cursor-pointer
                            border-b
                            border-gray-50
                        "
                    >

                        <p
                            class="
                                text-[14px]
                                font-semibold
                                text-gray-800
                            "
                        >
                            Shipment In Transit
                        </p>

                        <p
                            class="
                                text-[12px]
                                text-gray-400
                                mt-1
                            "
                        >
                            Order #ORD-2087 is now in transit.
                        </p>

                    </div>



                    <!-- ITEM 3 -->

                    <div
                        class="
                            px-5
                            py-4
                            hover:bg-gray-50
                            transition
                            cursor-pointer
                            border-b
                            border-gray-50
                        "
                    >

                        <p
                            class="
                                text-[14px]
                                font-semibold
                                text-gray-800
                            "
                        >
                            Low Stock Alert
                        </p>

                        <p
                            class="
                                text-[12px]
                                text-gray-400
                                mt-1
                            "
                        >
                            Wireless Headphones has only 10 units left.
                        </p>

                    </div>



                    <!-- ITEM 4 -->

                    <div
                        class="
                            px-5
                            py-4
                            hover:bg-gray-50
                            transition
                            cursor-pointer
                            border-b
                            border-gray-50
                        "
                    >

                        <p
                            class="
                                text-[14px]
                                font-semibold
                                text-gray-800
                            "
                        >
                            New Customer Feedback
                        </p>

                        <p
                            class="
                                text-[12px]
                                text-gray-400
                                mt-1
                            "
                        >
                            A customer left feedback on your product.
                        </p>

                    </div>

                </div>



                <!-- VIEW ALL -->

                <div
                    class="
                        px-5
                        py-3
                        text-center
                        border-t
                        border-gray-100
                    "
                >

                    <a
                        href="#"
                        class="
                            text-[13px]
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



        <!-- =================================================
             SELLER PROFILE
        ================================================== -->

        <div
            class="relative"
        >


            <!-- PROFILE BUTTON -->

            <button
                id="profile-button"
                type="button"
                class="
                    flex
                    items-center
                    gap-3
                    bg-white
                    rounded-[10px]
                    pl-3
                    pr-4
                    py-2
                    shadow-sm
                    border
                    border-gray-200
                    transition
                    hover:shadow-md
                    active:scale-[0.99]
                "
                aria-expanded="false"
                aria-controls="profile-dropdown"
            >


                <!-- PROFILE ICON -->

                <img
                    src="{{ asset('icons/seller/sidebar&navbar/seller-profile.png') }}"
                    class="
                        w-10
                        h-10
                        object-contain
                    "
                    alt="Seller Profile"
                >


                <!-- SELLER DETAILS -->

                <div
                    class="
                        text-sm
                        min-w-[150px]
                        text-left
                    "
                >

                    <p
                        class="
                            font-semibold
                            text-gray-800
                            leading-tight
                            text-[18px]
                        "
                    >
                        Seller
                    </p>

                    <p
                        class="
                            text-[13px]
                            text-gray-400
                            leading-tight
                        "
                    >
                        Seller Account
                    </p>

                </div>


                <!-- ARROW -->

                <svg
                    id="profile-chevron"
                    class="
                        w-5
                        h-5
                        text-gray-700
                        transition-transform
                        duration-200
                    "
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                    />

                </svg>

            </button>



            <!-- =================================================
                 PROFILE DROPDOWN
            ================================================== -->

            <div
                id="profile-dropdown"
                class="
                    hidden
                    absolute
                    right-0
                    top-[62px]
                    w-[250px]
                    bg-white
                    rounded-2xl
                    shadow-xl
                    border
                    border-gray-100
                    overflow-hidden
                    z-[60]
                "
            >


                <!-- PROFILE HEADER -->

                <div
                    class="
                        px-5
                        py-4
                        border-b
                        border-gray-100
                        flex
                        items-center
                        gap-3
                    "
                >

                    <img
                        src="{{ asset('icons/seller/sidebar&navbar/seller-profile.png') }}"
                        class="
                            w-10
                            h-10
                            object-contain
                        "
                        alt="Seller Profile"
                    >

                    <div>

                        <p
                            class="
                                text-[15px]
                                font-semibold
                                text-gray-800
                            "
                        >
                            Seller
                        </p>

                        <p
                            class="
                                text-[12px]
                                text-gray-400
                                mt-1
                            "
                        >
                            Seller Account
                        </p>

                    </div>

                </div>



                <!-- PROFILE OPTIONS -->

                <div
                    class="p-2"
                >


                    <!-- MY ACCOUNT -->

                    <a
                        href="#"
                        class="
                            flex
                            items-center
                            gap-3
                            px-3
                            py-3
                            rounded-xl
                            text-[14px]
                            text-gray-700
                            hover:bg-[#FFF2EE]
                            transition
                        "
                    >

                        <img
                            src="{{ asset('icons/seller/sidebar&navbar/account-management.png') }}"
                            class="
                                w-5
                                h-5
                                object-contain
                            "
                            alt=""
                        >

                        <span>
                            My Account
                        </span>

                    </a>



                    <!-- SETTINGS -->

                    <a
                        href="#"
                        class="
                            flex
                            items-center
                            gap-3
                            px-3
                            py-3
                            rounded-xl
                            text-[14px]
                            text-gray-700
                            hover:bg-[#FFF2EE]
                            transition
                        "
                    >

                        <span
                            class="
                                flex
                                items-center
                                justify-center
                                w-5
                                h-5
                            "
                        >

                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="
                                        M12 3v2
                                        M12 19v2
                                        M3 12h2
                                        M19 12h2
                                        M5.64 5.64l1.41 1.41
                                        M16.95 16.95l1.41 1.41
                                        M18.36 5.64l-1.41 1.41
                                        M7.05 16.95l-1.41 1.41
                                    "
                                />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="3.5"
                                />

                            </svg>

                        </span>

                        <span>
                            Settings
                        </span>

                    </a>

                </div>



                <!-- LOGOUT -->

                <div
                    class="
                        border-t
                        border-gray-100
                        p-2
                    "
                >

                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="
                                w-full
                                flex
                                items-center
                                gap-3
                                px-3
                                py-3
                                rounded-xl
                                text-[14px]
                                text-red-600
                                hover:bg-red-50
                                transition
                                text-left
                            "
                        >

                            <img
                                src="{{ asset('icons/seller/sidebar&navbar/log-out.png') }}"
                                class="
                                    w-5
                                    h-5
                                    object-contain
                                "
                                alt=""
                            >

                            <span>
                                Log Out
                            </span>

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</header>



<style>

/* =========================================================
   DYNAMIC NAVBAR POSITION
========================================================= */

/*
|--------------------------------------------------------------------------
| IMPORTANT
|--------------------------------------------------------------------------
|
| There is NO transition on margin-left.
|
| The sidebar itself can animate smoothly.
| The burger/title should immediately follow its current edge.
|
*/

.seller-navbar-left {

    margin-left:
        326px;

    transition:
        none !important;

}


/*
|--------------------------------------------------------------------------
| Reduced motion
|--------------------------------------------------------------------------
*/

@media (
    prefers-reduced-motion: reduce
) {

    .seller-navbar-left {

        transition:
            none !important;

    }

}

</style>



<!-- =========================================================
     SELLER NAVBAR JAVASCRIPT
========================================================= -->

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /* =====================================================
           ELEMENTS
        ====================================================== */

        const navbarLeft =
            document.getElementById(
                'navbar-left'
            );


        const sidebarToggle =
            document.getElementById(
                'sidebar-toggle'
            );


        const sellerSidebar =
            document.getElementById(
                'sellerSidebar'
            );


        const notificationButton =
            document.getElementById(
                'notification-button'
            );


        const notificationDropdown =
            document.getElementById(
                'notification-dropdown'
            );


        const profileButton =
            document.getElementById(
                'profile-button'
            );


        const profileDropdown =
            document.getElementById(
                'profile-dropdown'
            );


        const profileChevron =
            document.getElementById(
                'profile-chevron'
            );



        /* =====================================================
           UPDATE NAVBAR POSITION
        ====================================================== */

        function updateNavbarPosition() {

            if (
                !navbarLeft
            ) {

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Read actual sidebar position
            |--------------------------------------------------------------------------
            */

            if (
                sellerSidebar
            ) {

                const rect =
                    sellerSidebar.getBoundingClientRect();


                /*
                |--------------------------------------------------------------------------
                | Current visible right edge
                |--------------------------------------------------------------------------
                |
                | If the sidebar is fully hidden,
                | rect.right can be negative.
                |
                */

                const sidebarRight =
                    Math.max(
                        0,
                        rect.right
                    );


                /*
                |--------------------------------------------------------------------------
                | Gap between sidebar and burger
                |--------------------------------------------------------------------------
                */

                const spacing =
                    38;


                /*
                |--------------------------------------------------------------------------
                | INSTANT POSITION UPDATE
                |--------------------------------------------------------------------------
                */

                navbarLeft.style.marginLeft =
                    `${sidebarRight + spacing}px`;


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Fallback
            |--------------------------------------------------------------------------
            */

            navbarLeft.style.marginLeft =
                '326px';

        }



        /* =====================================================
           INITIAL POSITION
        ====================================================== */

        updateNavbarPosition();


        requestAnimationFrame(
            updateNavbarPosition
        );



        /* =====================================================
           SIDEBAR TOGGLE
        ====================================================== */

        if (
            sidebarToggle
        ) {

            sidebarToggle.addEventListener(
                'click',
                function () {

                    /*
                    |--------------------------------------------------------------------------
                    | Trigger sidebar
                    |--------------------------------------------------------------------------
                    */

                    document.body.dispatchEvent(
                        new CustomEvent(
                            'toggle-seller-sidebar'
                        )
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Immediately update once.
                    |--------------------------------------------------------------------------
                    */

                    updateNavbarPosition();


                    /*
                    |--------------------------------------------------------------------------
                    | Follow sidebar during its animation.
                    |--------------------------------------------------------------------------
                    |
                    | No navbar transition.
                    | We simply read the sidebar's current edge.
                    |
                    */

                    let frames =
                        0;


                    const maxFrames =
                        30;


                    function syncNavbar() {

                        updateNavbarPosition();


                        frames++;


                        if (
                            frames <
                            maxFrames
                        ) {

                            requestAnimationFrame(
                                syncNavbar
                            );

                        }

                    }


                    requestAnimationFrame(
                        syncNavbar
                    );

                }
            );

        }



        /* =====================================================
           RESIZE OBSERVER
        ====================================================== */

        if (
            sellerSidebar &&
            typeof ResizeObserver !==
            'undefined'
        ) {

            const sidebarResizeObserver =
                new ResizeObserver(
                    function () {

                        updateNavbarPosition();

                    }
                );


            sidebarResizeObserver.observe(
                sellerSidebar
            );

        }



        /* =====================================================
           MUTATION OBSERVER
        ====================================================== */

        if (
            sellerSidebar
        ) {

            const sidebarMutationObserver =
                new MutationObserver(
                    function () {

                        /*
                        |----------------------------------------------------------
                        | Update immediately.
                        |----------------------------------------------------------
                        */

                        updateNavbarPosition();


                        /*
                        |----------------------------------------------------------
                        | Follow the sidebar for a short frame window.
                        |----------------------------------------------------------
                        */

                        let frames =
                            0;


                        const maxFrames =
                            30;


                        function syncAfterMutation() {

                            updateNavbarPosition();


                            frames++;


                            if (
                                frames <
                                maxFrames
                            ) {

                                requestAnimationFrame(
                                    syncAfterMutation
                                );

                            }

                        }


                        requestAnimationFrame(
                            syncAfterMutation
                        );

                    }
                );


            sidebarMutationObserver.observe(
                sellerSidebar,
                {
                    attributes:
                        true,

                    attributeFilter: [
                        'class',
                        'style'
                    ]
                }
            );

        }



        /* =====================================================
           WINDOW RESIZE
        ====================================================== */

        window.addEventListener(
            'resize',
            function () {

                updateNavbarPosition();

            }
        );



        /* =====================================================
           NOTIFICATION DROPDOWN
        ====================================================== */

        if (
            notificationButton &&
            notificationDropdown
        ) {

            notificationButton.addEventListener(
                'click',
                function (event) {

                    event.stopPropagation();


                    /*
                    |--------------------------------------------------------------------------
                    | Close profile
                    |--------------------------------------------------------------------------
                    */

                    if (
                        profileDropdown
                    ) {

                        profileDropdown.classList.add(
                            'hidden'
                        );

                    }


                    if (
                        profileButton
                    ) {

                        profileButton.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                    }


                    if (
                        profileChevron
                    ) {

                        profileChevron.classList.remove(
                            'rotate-180'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Toggle notification
                    |--------------------------------------------------------------------------
                    */

                    const isHidden =
                        notificationDropdown.classList.contains(
                            'hidden'
                        );


                    notificationDropdown.classList.toggle(
                        'hidden'
                    );


                    notificationButton.setAttribute(
                        'aria-expanded',
                        String(
                            isHidden
                        )
                    );

                }
            );

        }



        /* =====================================================
           PROFILE DROPDOWN
        ====================================================== */

        if (
            profileButton &&
            profileDropdown
        ) {

            profileButton.addEventListener(
                'click',
                function (event) {

                    event.stopPropagation();


                    /*
                    |--------------------------------------------------------------------------
                    | Close notification
                    |--------------------------------------------------------------------------
                    */

                    if (
                        notificationDropdown
                    ) {

                        notificationDropdown.classList.add(
                            'hidden'
                        );

                    }


                    if (
                        notificationButton
                    ) {

                        notificationButton.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Toggle profile
                    |--------------------------------------------------------------------------
                    */

                    const isHidden =
                        profileDropdown.classList.contains(
                            'hidden'
                        );


                    profileDropdown.classList.toggle(
                        'hidden'
                    );


                    profileButton.setAttribute(
                        'aria-expanded',
                        String(
                            isHidden
                        )
                    );


                    if (
                        profileChevron
                    ) {

                        profileChevron.classList.toggle(
                            'rotate-180',
                            isHidden
                        );

                    }

                }
            );

        }



        /* =====================================================
           CLICK OUTSIDE
        ====================================================== */

        document.addEventListener(
            'click',
            function (event) {


                /*
                |--------------------------------------------------------------------------
                | Notification
                |--------------------------------------------------------------------------
                */

                if (
                    notificationDropdown &&
                    !notificationDropdown.contains(
                        event.target
                    ) &&
                    notificationButton &&
                    !notificationButton.contains(
                        event.target
                    )
                ) {

                    notificationDropdown.classList.add(
                        'hidden'
                    );


                    notificationButton.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Profile
                |--------------------------------------------------------------------------
                */

                if (
                    profileDropdown &&
                    !profileDropdown.contains(
                        event.target
                    ) &&
                    profileButton &&
                    !profileButton.contains(
                        event.target
                    )
                ) {

                    profileDropdown.classList.add(
                        'hidden'
                    );


                    profileButton.setAttribute(
                        'aria-expanded',
                        'false'
                    );


                    if (
                        profileChevron
                    ) {

                        profileChevron.classList.remove(
                            'rotate-180'
                        );

                    }

                }

            }
        );



        /* =====================================================
           ESC KEY
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


                /*
                |--------------------------------------------------------------------------
                | Notification
                |--------------------------------------------------------------------------
                */

                if (
                    notificationDropdown
                ) {

                    notificationDropdown.classList.add(
                        'hidden'
                    );

                }


                if (
                    notificationButton
                ) {

                    notificationButton.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Profile
                |--------------------------------------------------------------------------
                */

                if (
                    profileDropdown
                ) {

                    profileDropdown.classList.add(
                        'hidden'
                    );

                }


                if (
                    profileButton
                ) {

                    profileButton.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                }


                if (
                    profileChevron
                ) {

                    profileChevron.classList.remove(
                        'rotate-180'
                    );

                }

            }
        );

    }
);

</script>