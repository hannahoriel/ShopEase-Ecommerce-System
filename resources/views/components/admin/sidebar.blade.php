<aside class="fixed left-0 top-0 z-50 w-72 h-screen overflow-y-auto bg-gradient-to-b from-maroon-900 to-maroon-950 rounded-tr-[4rem] flex flex-col justify-between py-8 px-5 text-white">
    <div>
        <div class="flex items-center justify-center mb-10 px-2 -mt-4">
            <img src="{{ asset('icons/admin/dashboard/sidebar&navbar/shopease.png') }}" alt="ShopEase" class="h-20 w-auto">
        </div>

        <nav class="space-y-1">
            @php
                $menu = [
                    ['label' => 'Dashboard', 'icon' => 'dashboard.png', 'active' => true],
                    ['label' => 'Registrations', 'icon' => 'registrations.png'],
                    ['label' => 'User Management', 'icon' => 'user-management.png'],
                    ['label' => 'Seller Compliance', 'icon' => 'seller-compliance.png'],
                    ['label' => 'Complaints and Disputes', 'icon' => 'complaints-disputes.png'],
                    ['label' => 'Commission', 'icon' => 'commission.png'],
                    ['label' => 'Reports', 'icon' => 'reports.png'],
                    ['label' => 'Platform Settings', 'icon' => 'settings.png'],
                    ['label' => 'Messages', 'icon' => 'messages.png'],
                    ['label' => 'Account Management', 'icon' => 'account-management.png'],
                ];
            @endphp

            @foreach ($menu as $item)
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-full transition {{ ($item['active'] ?? false) ? 'bg-maroon-700/60' : 'hover:bg-maroon-800/50' }}">
                    <img src="{{ asset('icons/admin/dashboard/sidebar&navbar/' . $item['icon']) }}" class="w-5 h-5" alt="">
                    <span class="text-base font-medium">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <a href="#" class="flex items-center justify-center gap-2 border border-white/40 rounded-full py-3 text-sm font-medium hover:bg-white/10">
        <img src="{{ asset('icons/admin/dashboard/sidebar&navbar/log-out.png') }}" class="w-4 h-4" alt="">
        Log Out
    </a>
</aside>