<aside
    id="admin-sidebar"
    class="
        fixed left-0 top-0 z-50
        w-72 h-screen
        overflow-y-auto
        bg-gradient-to-b from-maroon-900 to-maroon-950
        rounded-tr-[4rem]
        flex flex-col justify-between
        py-8 px-5
        text-white
        transition-all duration-300
        sidebar-reload
    "
>

    <div>

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
                        'active' => true
                    ],

                    [
                        'label' => 'Registrations',
                        'icon' => 'registrations.png'
                    ],

                    [
                        'label' => 'User Management',
                        'icon' => 'user-management.png'
                    ],

                    [
                        'label' => 'Seller Compliance',
                        'icon' => 'seller-compliance.png'
                    ],

                    [
                        'label' => 'Complaints and Disputes',
                        'icon' => 'complaints-disputes.png'
                    ],

                    [
                        'label' => 'Commission',
                        'icon' => 'commission.png'
                    ],

                    [
                        'label' => 'Reports',
                        'icon' => 'reports.png'
                    ],

                    [
                        'label' => 'Platform Settings',
                        'icon' => 'settings.png'
                    ],

                    [
                        'label' => 'Messages',
                        'icon' => 'messages.png'
                    ],

                    [
                        'label' => 'Account Management',
                        'icon' => 'account-management.png'
                    ],

                ];

            @endphp


            @foreach ($menu as $item)

                <a
                    href="#"
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

                        {{ ($item['active'] ?? false)
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

            @endforeach

        </nav>

    </div>


    <!-- ==================== LOGOUT ==================== -->

    <a
        href="#"
        class="
            sidebar-logout
            sidebar-logout-reload
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