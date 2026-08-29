<aside
    id="admin-sidebar"
    class="
        fixed left-0 top-0 z-50
        w-72 h-screen
        overflow-hidden
        bg-gradient-to-b from-maroon-900 to-maroon-950
        rounded-tr-[4rem]
        flex flex-col
        py-8 px-5
        text-white
        transition-all duration-300
        sidebar-reload
    "
>

    <div class="flex-1 min-h-0">
        <!-- ==================== LOGO ==================== -->

        <div
            id="sidebar-logo"
            class="
                flex items-center justify-center
                mb-10 px-2 -mt-4
                transition-all duration-500
                sidebar-logo-reload
            "
        >

            <img
                src="{{ asset('icons/admin/dashboard/sidebar&navbar/shopease.png') }}"
                alt="ShopEase"
                class="h-20 w-auto"
            >

        </div>


        <!-- ==================== NAVIGATION ==================== -->

        <nav class="space-y-1">

            @php

                $menu = [

                    [
                        'label' => 'Dashboard',
                        'icon' => 'dashboard.png',
                        'route' => 'admin.dashboard'
                    ],

                    [
                        'label' => 'Registrations',
                        'icon' => 'registrations.png',
                        'route' => 'admin.registrations'
                    ],

                    [
                        'label' => 'User Management',
                        'icon' => 'user-management.png',
                        'route' => 'admin.user.management'
                    ],

                    [
                        'label' => 'Seller Compliance',
                        'icon' => 'seller-compliance.png',
                        'route' => null
                    ],

                    [
                        'label' => 'Complaints and Disputes',
                        'icon' => 'complaints-disputes.png',
                        'route' => null
                    ],

                    [
                        'label' => 'Commission',
                        'icon' => 'commission.png',
                        'route' => null
                    ],

                    [
    'label' => 'Logistics Management',
    'icon' => 'logistic-management.png',
    'route' => null
],

                    [
                        'label' => 'Reports',
                        'icon' => 'reports.png',
                        'route' => null
                    ],

                    [
                        'label' => 'Platform Settings',
                        'icon' => 'settings.png',
                        'route' => null
                    ],

                    [
                        'label' => 'Messages',
                        'icon' => 'messages.png',
                        'route' => null
                    ],

                    [
                        'label' => 'Account Management',
                        'icon' => 'account-management.png',
                        'route' => null
                    ],

                ];

            @endphp


            @foreach ($menu as $item)

                @php

                    /*
                    |--------------------------------------------------------------------------
                    | Check if current page is active
                    |--------------------------------------------------------------------------
                    */

                    $isActive = $item['route']
                        ? request()->routeIs($item['route'])
                        : false;

                @endphp


                @if ($item['route'])

                    <!-- ==================== ACTIVE / AVAILABLE PAGE ==================== -->

                    <a
                        href="{{ route($item['route']) }}"
                        class="
                            sidebar-link
                            sidebar-menu-item
                            flex items-center
                            gap-3
                            px-4
                            py-3
                            rounded-full
                            transition-all
                            duration-300
                            w-full

                            {{ $isActive
                                ? 'bg-maroon-700/60'
                                : 'hover:bg-maroon-800/50 hover:translate-x-1'
                            }}
                        "
                    >

                        <!-- ICON -->

                        <span
                            class="
                                sidebar-icon-wrapper
                                flex items-center justify-center
                                shrink-0
                                w-5 h-5
                                transition-all duration-300
                            "
                        >

                            <img
                                src="{{ asset('icons/admin/dashboard/sidebar&navbar/' . $item['icon']) }}"
                                class="
                                    sidebar-icon
                                    w-5 h-5
                                    object-contain
                                    shrink-0
                                "
                                alt=""
                            >

                        </span>


                        <!-- LABEL -->

                        <span
                            class="
                                sidebar-label
                                text-[16px]
                                font-medium
                                whitespace-nowrap
                            "
                        >
                            {{ $item['label'] }}
                        </span>

                    </a>

                @else

                    <!-- ==================== NOT YET AVAILABLE ==================== -->

                    <a
                        href="#"
                        onclick="return false;"
                        class="
                            sidebar-link
                            sidebar-menu-item
                            flex items-center
                            gap-3
                            px-4
                            py-3
                            rounded-full
                            transition-all
                            duration-300
                            w-full
                            opacity-100
                            cursor-default
                        "
                    >

                        <!-- ICON -->

                        <span
                            class="
                                sidebar-icon-wrapper
                                flex items-center justify-center
                                shrink-0
                                w-5 h-5
                            "
                        >

                            <img
                                src="{{ asset('icons/admin/dashboard/sidebar&navbar/' . $item['icon']) }}"
                                class="
                                    sidebar-icon
                                    w-5 h-5
                                    object-contain
                                    shrink-0
                                "
                                alt=""
                            >

                        </span>


                        <!-- LABEL -->

                        <span
                            class="
                                sidebar-label
                                text-[16px]
                                font-medium
                                whitespace-nowrap
                            "
                        >
                            {{ $item['label'] }}
                        </span>

                    </a>

                @endif

            @endforeach

        </nav>

    </div>


    <!-- ==================== LOGOUT ==================== -->

    <a
    href="#"
    class="
        sidebar-logout
        sidebar-logout-reload
        shrink-0
        flex items-center justify-center
        gap-2
        border border-white/40
        rounded-full
        py-3
        text-[15px]
        font-medium
        hover:bg-white/10
        transition-all duration-300
        w-full
    "
>

        <span
            class="
                sidebar-icon-wrapper
                flex items-center justify-center
                shrink-0
                w-5 h-5
            "
        >

            <img
                src="{{ asset('icons/admin/dashboard/sidebar&navbar/log-out.png') }}"
                class="
                    sidebar-icon
                    w-4 h-4
                    object-contain
                    shrink-0
                "
                alt=""
            >

        </span>


        <span class="sidebar-label">
            Log Out
        </span>

    </a>

</aside>

<style>
    /* =========================================================
       SIDEBAR RELOAD UX
       Animates menu items from LEFT to RIGHT
       while keeping TOP-TO-BOTTOM order
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

    /* =========================================================
       STRICT TOP-TO-BOTTOM ORDER
    ========================================================== */

    .sidebar-menu-item:nth-child(1) {
        animation-delay: 0.05s;
    }

    .sidebar-menu-item:nth-child(2) {
        animation-delay: 0.10s;
    }

    .sidebar-menu-item:nth-child(3) {
        animation-delay: 0.15s;
    }

    .sidebar-menu-item:nth-child(4) {
        animation-delay: 0.20s;
    }

    .sidebar-menu-item:nth-child(5) {
        animation-delay: 0.25s;
    }

    .sidebar-menu-item:nth-child(6) {
        animation-delay: 0.30s;
    }

    .sidebar-menu-item:nth-child(7) {
        animation-delay: 0.35s;
    }

    .sidebar-menu-item:nth-child(8) {
        animation-delay: 0.40s;
    }

    .sidebar-menu-item:nth-child(9) {
        animation-delay: 0.45s;
    }

    .sidebar-menu-item:nth-child(10) {
        animation-delay: 0.50s;
    }

    .sidebar-menu-item:nth-child(11) {
        animation-delay: 0.55s;
    }
</style>