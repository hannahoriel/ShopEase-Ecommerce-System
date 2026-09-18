<header
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

    <!-- ==================== LEFT SIDE ==================== -->

    <div
        id="navbar-left"
        class="
            ml-[304px]
            flex
            items-center
            gap-6
            transition-all
            duration-300
        "
    >

        <!-- Sidebar Toggle -->

        <button
            id="sidebar-toggle"
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
            aria-label="Toggle sidebar"
        >

            <svg
                class="w-6 h-6"
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


        <!-- Page Title -->

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


    <!-- ==================== RIGHT SIDE ==================== -->

    <div class="flex items-center gap-6 mr-[28px]">


        <!-- ==================== NOTIFICATIONS ==================== -->

        <div class="relative">

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
            >

                <img
                    src="{{ asset('icons/admin/dashboard/sidebar&navbar/notification.png') }}"
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


            <!-- Notification Dropdown -->

            <div
                id="notification-dropdown"
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
                            New seller registration
                        </p>

                        <p class="text-[11px] text-gray-400 mt-1">
                            A new seller is waiting for approval.
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
                            New complaint received
                        </p>

                        <p class="text-[11px] text-gray-400 mt-1">
                            A complaint requires your attention.
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
                            Pending registrations
                        </p>

                        <p class="text-[11px] text-gray-400 mt-1">
                            There are pending registrations to review.
                        </p>

                    </div>

                </div>


                <div class="px-4 py-2.5 text-center border-t border-gray-100">

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


        <!-- ==================== ADMIN PROFILE ==================== -->

        <div class="relative">

            <button
                id="profile-button"
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
            >

                <img
                    src="{{ asset('icons/admin/dashboard/sidebar&navbar/admin-profile.png') }}"
                    class="w-9 h-9 object-contain"
                    alt="Admin Profile"
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
                        Admin
                    </p>

                    <p class="text-[12px] text-gray-400 leading-tight">
                        Administrator Account
                    </p>

                </div>


                <!-- Arrow -->

                <svg
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
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                    />

                </svg>

            </button>


            <!-- Profile Dropdown -->

            <div
                id="profile-dropdown"
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
                        Admin
                    </p>

                    <p class="text-[11px] text-gray-400 mt-1">
                        Administrator Account
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

                    <form action="{{ route('logout') }}" method="POST">

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


<!-- =========================================================
     NAVBAR ↔ SIDEBAR RESPONSIVE POSITION
========================================================= -->

<style>
    /*
     * Sidebar OPEN
     * navbar-left starts at 304px
     */
    #navbar-left {
        margin-left: 304px;
    }

    /*
     * Sidebar COLLAPSED
     * sidebar width becomes ~80px,
     * so navbar content moves closer to the left.
     */
    body.sidebar-collapsed #navbar-left {
        margin-left: 128px !important;
    }

    /*
     * Support common sidebar state classes.
     * This allows the navbar to follow the sidebar even when
     * the sidebar state is controlled by app.js.
     */
    body.sidebar-hidden #navbar-left,
    body.sidebar-collapsed #navbar-left,
    body.sidebar-closed #navbar-left {
        margin-left: 128px !important;
    }

    /*
     * Mobile
     */
    @media (max-width: 900px) {
        #navbar-left {
            margin-left: 104px !important;
        }

        body.sidebar-collapsed #navbar-left,
        body.sidebar-hidden #navbar-left,
        body.sidebar-closed #navbar-left {
            margin-left: 24px !important;
        }
    }

    @media (max-width: 640px) {
        #navbar-left {
            margin-left: 20px !important;
            gap: 12px;
        }

        #navbar-left h1 {
            font-size: 18px;
        }

        header > div:last-child {
            margin-right: 14px;
            gap: 12px;
        }
    }
</style>


<!-- =========================================================
     NAVBAR SIDEBAR TOGGLE SYNC
========================================================= -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebarToggle = document.getElementById('sidebar-toggle');
    const navbarLeft = document.getElementById('navbar-left');

    const sidebar =
        document.getElementById('admin-sidebar') ||
        document.getElementById('adminSidebar') ||
        document.getElementById('sellerSidebar') ||
        document.querySelector('[data-admin-sidebar]') ||
        document.querySelector('.admin-sidebar');

    if (!sidebarToggle || !navbarLeft) return;


    function updateNavbarPosition() {

        /*
         * Check common collapsed/hidden states.
         */
        const bodyCollapsed =
            document.body.classList.contains('sidebar-collapsed') ||
            document.body.classList.contains('sidebar-hidden') ||
            document.body.classList.contains('sidebar-closed');

        const sidebarCollapsed =
            sidebar &&
            (
                sidebar.classList.contains('w-20') ||
                sidebar.classList.contains('w-[80px]') ||
                sidebar.classList.contains('collapsed') ||
                sidebar.classList.contains('sidebar-collapsed')
            );

        /*
         * If sidebar is collapsed, move navbar-left.
         */
        if (bodyCollapsed || sidebarCollapsed) {
            navbarLeft.style.marginLeft = '128px';
        } else {
            navbarLeft.style.marginLeft = '304px';
        }
    }


    /*
     * Update immediately when page loads.
     */
    updateNavbarPosition();


    /*
     * Run after sidebar toggle.
     * setTimeout allows the existing sidebar app.js logic
     * to finish updating its classes first.
     */
    sidebarToggle.addEventListener('click', function () {
        setTimeout(updateNavbarPosition, 20);
        setTimeout(updateNavbarPosition, 150);
        setTimeout(updateNavbarPosition, 320);
    });


    /*
     * Watch sidebar class changes.
     * This keeps the burger/title synchronized even when
     * sidebar state is changed by another script.
     */
    if (sidebar) {

        const observer = new MutationObserver(function () {
            updateNavbarPosition();
        });

        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class', 'style']
        });
    }


    /*
     * Watch body class changes too.
     */
    const bodyObserver = new MutationObserver(function () {
        updateNavbarPosition();
    });

    bodyObserver.observe(document.body, {
        attributes: true,
        attributeFilter: ['class']
    });

});
</script>