<header
    class="
        fixed
        top-0
        left-0
        right-0
        z-10
        h-[104px]
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
            ml-[338px]
            flex
            items-center
            gap-9
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


        <!-- Page Title -->

        <h1
            class="
                text-[24px]
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

    <div class="flex items-center gap-8 mr-[42px]">


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
                    class="w-7 h-8 object-contain"
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


            <!-- Notification Dropdown -->

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

                    <h3 class="text-[16px] font-bold text-gray-800">
                        Notifications
                    </h3>

                    <span class="text-xs text-maroon-700 font-medium">
                        4 new
                    </span>

                </div>


                <div class="max-h-[300px] overflow-y-auto">

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

                        <p class="text-[14px] font-semibold text-gray-800">
                            New seller registration
                        </p>

                        <p class="text-[12px] text-gray-400 mt-1">
                            A new seller is waiting for approval.
                        </p>

                    </div>


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

                        <p class="text-[14px] font-semibold text-gray-800">
                            New complaint received
                        </p>

                        <p class="text-[12px] text-gray-400 mt-1">
                            A complaint requires your attention.
                        </p>

                    </div>


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

                        <p class="text-[14px] font-semibold text-gray-800">
                            Pending registrations
                        </p>

                        <p class="text-[12px] text-gray-400 mt-1">
                            There are pending registrations to review.
                        </p>

                    </div>

                </div>


                <div class="px-5 py-3 text-center border-t border-gray-100">

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


        <!-- ==================== ADMIN PROFILE ==================== -->

        <div class="relative">

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
            >

                <img
                    src="{{ asset('icons/admin/dashboard/sidebar&navbar/admin-profile.png') }}"
                    class="w-10 h-10 object-contain"
                    alt="Admin Profile"
                >


                <div class="text-sm min-w-[150px] text-left">

                    <p
                        class="
                            font-semibold
                            text-gray-800
                            leading-tight
                            text-[18px]
                        "
                    >
                        Admin
                    </p>

                    <p class="text-[13px] text-gray-400 leading-tight">
                        Administrator Account
                    </p>

                </div>


                <!-- Arrow -->

                <svg
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


            <!-- Profile Dropdown -->

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

                <div class="px-5 py-4 border-b border-gray-100">

                    <p class="text-[15px] font-semibold text-gray-800">
                        Admin
                    </p>

                    <p class="text-[12px] text-gray-400 mt-1">
                        Administrator Account
                    </p>

                </div>


                <div class="p-2">

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
                        <span>My Account</span>
                    </a>


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
                        <span>Settings</span>
                    </a>

                </div>


                <div class="border-t border-gray-100 p-2">

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
                            text-red-600
                            hover:bg-red-50
                            transition
                        "
                    >
                        <span>Log Out</span>
                    </a>

                </div>

            </div>

        </div>

    </div>

</header>