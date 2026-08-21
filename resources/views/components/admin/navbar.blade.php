<header class="fixed top-0 left-0 right-0 z-10 h-[104px] bg-[#FFE6DE] flex items-center justify-between">

    <!-- Left side: offset only the navbar CONTENT -->
    <div class="ml-[338px] flex items-center gap-9">
        <button class="text-maroon-900 flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                />
            </svg>
        </button>

        <h1 class="text-[24px] font-bold text-gray-800 leading-none">
            @yield('page-title', 'Dashboard')
        </h1>
    </div>

    <!-- Right side -->
    <div class="flex items-center gap-8 mr-[42px]">

        <!-- Notification -->
        <div class="relative flex items-center justify-center">
            <img
                src="{{ asset('icons/admin/dashboard/sidebar&navbar/notification.png') }}"
                class="w-7 h-8"
                alt="Notifications"
            >

            <span class="absolute -top-2 -right-2 bg-red-600 text-white text-[11px] font-semibold rounded-full w-5 h-5 flex items-center justify-center">
                4
            </span>
        </div>

        <!-- Admin Profile -->
        <div class="flex items-center gap-3 bg-white rounded-[10px] pl-3 pr-4 py-2 shadow-sm border border-gray-200">

            <img
                src="{{ asset('icons/admin/dashboard/sidebar&navbar/admin-profile.png') }}"
                class="w-10 h-10"
                alt="Admin Profile"
            >

            <div class="text-sm min-w-[150px]">
                <p class="font-semibold text-gray-800 leading-tight text-[18px]">
                    Admin
                </p>

                <p class="text-[13px] text-gray-400 leading-tight">
                    Administrator Account
                </p>
            </div>

            <svg
                class="w-5 h-5 text-gray-700"
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

        </div>

    </div>

</header>