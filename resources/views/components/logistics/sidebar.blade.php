{{-- Logistics sidebar: styling and motion matched to the supplied Admin sidebar. --}}
@php
    $lgNavItems = [
        ['label'=>'Dashboard','icon'=>'dashboard.png','href'=>url('/logistics/dashboard'),'active'=>request()->routeIs('logistics.dashboard'),'enabled'=>true],
        ['label'=>'Network Hubs','icon'=>'logistic-management.png','href'=>\Illuminate\Support\Facades\Route::has('logistics.deliveries') ? route('logistics.deliveries') : url('/logistics/deliveries'),'active'=>request()->routeIs('logistics.deliveries') || request()->routeIs('logistics.network-hubs'),'enabled'=>true],
        ['label'=>'Finance Review','icon'=>'commission.png','href'=>\Illuminate\Support\Facades\Route::has('logistics.rider-operations') ? route('logistics.rider-operations') : '#','active'=>request()->routeIs('logistics.rider-operations'),'enabled'=>\Illuminate\Support\Facades\Route::has('logistics.rider-operations')],
        ['label'=>'Messages','icon'=>'messages.png','href'=>route('logistics.messages'),'active'=>request()->routeIs('logistics.messages'),'enabled'=>true],
        ['label'=>'Reports','icon'=>'reports.png','href'=>'#','active'=>false,'enabled'=>false],
        ['label'=>'Parcel Tracking','icon'=>'reports.png','href'=>\Illuminate\Support\Facades\Route::has('logistics.parcel-tracking') ? route('logistics.parcel-tracking') : '#','active'=>request()->routeIs('logistics.parcel-tracking'),'enabled'=>\Illuminate\Support\Facades\Route::has('logistics.parcel-tracking')],
        ['label'=>'Logistics Earnings','icon'=>'commission.png','href'=>'#','active'=>false,'enabled'=>false],
        ['label'=>'Account','icon'=>'account-management.png','href'=>\Illuminate\Support\Facades\Route::has('logistics.account') ? route('logistics.account') : url('/logistics/account'),'active'=>request()->routeIs('logistics.account'),'enabled'=>true],
    ];
@endphp
<aside id="lgSidebar" class="lg-sidebar" aria-label="Logistics navigation">
    <div class="lg-sidebar-content">
        <a class="lg-sidebar-logo" href="{{ url('/logistics/dashboard') }}" aria-label="ShopEase Logistics Dashboard">
            <img src="{{ asset('icons/admin/dashboard/sidebar&navbar/shopease.png') }}" alt="ShopEase" onerror="this.style.display='none'">
        </a>
        <nav class="lg-sidebar-menu" aria-label="Logistics menu">
            @php
                $lgEntryIndex = 0;
                $lgGroups = [
                    ['heading' => 'OVERVIEW', 'labels' => ['Dashboard']],
                    ['heading' => 'NETWORK MANAGEMENT', 'labels' => ['Network Hubs', 'Finance Review', 'Parcel Tracking', 'Messages', 'Reports']],
                    ['heading' => 'FINANCE & ACCOUNT', 'labels' => ['Logistics Earnings', 'Account']],
                ];
            @endphp
            {{-- Plain PHP loops prevent Blade's inline array parsing from generating a stray endforeach. --}}
            <?php foreach ($lgGroups as $group): ?>
                <p class="lg-nav-category lg-admin-group-heading">{{ $group['heading'] }}</p>
                <?php foreach ($group['labels'] as $navLabel): ?>
                    <?php
                        $item = collect($lgNavItems)->firstWhere('label', $navLabel);
                        $lgEntryIndex++;
                        $lgAnimDelay = number_format(.05 + ($lgEntryIndex - 1) * .05, 2, '.', '');
                    ?>
                    @if($item && $item['enabled'])
                        <a class="lg-nav-link lg-admin-menu-item {{ $item['active'] ? 'active' : '' }}" href="{{ $item['href'] }}" title="{{ $item['label'] }}" @if($item['active']) aria-current="page" @endif style="animation-delay:{{ $lgAnimDelay }}s">
                            <span class="lg-admin-icon-wrap"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/' . $item['icon']) }}" alt=""></span>
                            <span class="lg-admin-label">{{ $item['label'] }}</span>
                        </a>
                    @elseif($item)
                        <span class="lg-nav-link lg-nav-unavailable lg-admin-menu-item" aria-disabled="true" title="Not available yet" style="animation-delay:{{ $lgAnimDelay }}s">
                            <span class="lg-admin-icon-wrap"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/' . $item['icon']) }}" alt=""></span>
                            <span class="lg-admin-label">{{ $item['label'] }}</span>
                        </span>
                    @endif
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
    </div>
    @if(\Illuminate\Support\Facades\Route::has('logout'))
        <form id="lgSidebarLogoutForm" class="lg-sidebar-footer" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="lg-sidebar-logout lg-admin-logout-enter" title="Log Out">
                <span class="lg-admin-icon-wrap"><img src="{{ asset('icons/admin/dashboard/sidebar&navbar/log-out.png') }}" alt=""></span>
                <span class="lg-logout-label">Log Out</span>
            </button>
        </form>
    @endif
</aside>
<style>
/* Hover fix: reserve 5px of horizontal space for the 4px translation so the right pill curve remains visible.
   Entrance translation is moved to children to avoid a filled animation overriding :hover transforms. */
/* The original Admin sidebar uses a top-to-bottom maroon-900 → maroon-950 gradient,
   translucent maroon hover/active pills, and 300ms slide animation. Scoped to Logistics. */
#lgSidebar.lg-sidebar{
    background:linear-gradient(to bottom,#651015 0%,#360609 100%)!important;
    border-top-right-radius:64px!important;
    padding:24px 16px!important;
    display:flex!important;flex-direction:column!important;
    color:#fff!important;overflow:hidden!important;
    transition:width .3s ease,transform .3s ease!important;
}
#lgSidebar .lg-sidebar-content{flex:1 1 auto;min-height:0;display:flex;flex-direction:column}
#lgSidebar .lg-sidebar-logo{display:flex;justify-content:center;align-items:center;height:auto;min-height:72px;margin:-8px 0 28px;padding:0 4px;gap:0;white-space:nowrap;transition:all .5s ease}
#lgSidebar .lg-sidebar-logo img{height:64px;width:auto;max-width:100%;max-height:64px;object-fit:contain}
#lgSidebar .lg-sidebar-menu{display:flex;flex-direction:column;gap:2px;flex:1;min-height:0;overflow-y:auto;overflow-x:hidden;scrollbar-width:none;-ms-overflow-style:none}
#lgSidebar .lg-sidebar-menu::-webkit-scrollbar{display:none}
/* Restored grouped section labels, with the same muted light-maroon tone as the original Logistics sidebar. */
#lgSidebar .lg-admin-group-heading{display:block;margin:17px 12px 8px!important;color:#f5c8c7!important;font-size:10px!important;letter-spacing:1.4px!important;font-weight:600!important;line-height:1.4;white-space:nowrap;flex-shrink:0}
#lgSidebar .lg-admin-group-heading:first-child{margin-top:5px!important}
body.lg-collapsed #lgSidebar .lg-admin-group-heading{display:none}
@media (max-width:900px) and (min-width:701px){body:not(.lg-collapsed) #lgSidebar .lg-admin-group-heading{display:none}body.lg-collapsed #lgSidebar .lg-admin-group-heading{display:block}}
@media(max-width:700px){body.lg-collapsed #lgSidebar .lg-admin-group-heading{display:block}}
#lgSidebar .lg-nav-link{
    height:auto!important;min-height:40px;width:calc(100% - 5px);margin:0 5px 0 0!important;
    padding:10px 12px!important;display:flex;align-items:center;justify-content:flex-start;
    gap:10px!important;border-radius:999px!important;border:0!important;
    color:#fff!important;background:transparent;font-size:14px!important;font-weight:500!important;
    white-space:nowrap;text-align:left;line-height:1.45;
    transition:background-color .3s ease,transform .3s ease!important;will-change:transform;
}
#lgSidebar .lg-nav-link:not(.lg-nav-unavailable):not(.active):hover{background:rgba(112,25,31,.50)!important;transform:translateX(4px)!important;box-shadow:inset 0 0 0 1px rgba(255,255,255,.045)}
#lgSidebar .lg-nav-link.active{background:rgba(139,40,46,.60)!important;transform:none}
#lgSidebar .lg-nav-link.active:hover{background:rgba(151,45,53,.73)!important;transform:translateX(4px)!important}
#lgSidebar .lg-nav-unavailable{opacity:1!important;cursor:default!important;pointer-events:none}
#lgSidebar .lg-nav-unavailable:hover{background:transparent!important;transform:none!important}
#lgSidebar .lg-admin-icon-wrap{width:18px;height:18px;flex:0 0 18px;display:flex;justify-content:center;align-items:center;transition:all .3s ease}
#lgSidebar .lg-admin-icon-wrap img{width:18px;height:18px;object-fit:contain;display:block}
#lgSidebar .lg-admin-label{font-size:14px;font-weight:500;white-space:nowrap}
#lgSidebar .lg-sidebar-footer{width:100%;flex:0 0 auto;margin-top:auto!important;padding:0!important;border:0!important}
#lgSidebar .lg-sidebar-logout{width:100%;height:auto;min-height:42px;padding:10px 12px;border:1px solid rgba(255,255,255,.58)!important;background:transparent!important;border-radius:999px;color:white;display:flex;justify-content:center;align-items:center;gap:8px;font:500 14px Poppins,sans-serif;cursor:pointer;transition:background .3s ease,border-color .3s ease!important}
#lgSidebar .lg-sidebar-logout:hover{background:rgba(255,255,255,.08)!important;border-color:rgba(255,255,255,.82)!important}
#lgSidebar .lg-sidebar-logout .lg-admin-icon-wrap img{width:15px;height:15px}
@keyframes lgAdminMenuEnter{from{opacity:0}to{opacity:1}}
@keyframes lgAdminMenuContentEnter{from{opacity:0;transform:translateX(-20px)}to{opacity:1;transform:translateX(0)}}
@keyframes lgAdminLogoutEnter{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
#lgSidebar .lg-admin-menu-item{animation:lgAdminMenuEnter .35s ease-out both}
#lgSidebar .lg-admin-menu-item .lg-admin-icon-wrap,#lgSidebar .lg-admin-menu-item .lg-admin-label{animation:lgAdminMenuContentEnter .35s ease-out both;animation-delay:inherit}
#lgSidebar .lg-admin-logout-enter{opacity:0;animation:lgAdminLogoutEnter .45s ease-out .6s forwards}
/* Preserve existing Logistics Dashboard/Network Hubs navbar toggle without changing their scripts. */
body.lg-collapsed #lgSidebar .lg-sidebar-logo{min-height:72px}
body.lg-collapsed #lgSidebar .lg-sidebar-logo img{max-width:52px;height:auto}
body.lg-collapsed #lgSidebar .lg-nav-link{justify-content:center;padding:10px 0!important;width:100%;margin-right:0!important;transform:none!important}
body.lg-collapsed #lgSidebar .lg-admin-label{display:none}
body.lg-collapsed #lgSidebar .lg-sidebar-footer{padding:0!important}
body.lg-collapsed #lgSidebar .lg-sidebar-logout{width:44px;height:44px;margin:0 auto;padding:0}
body.lg-collapsed #lgSidebar .lg-logout-label{display:none}
@media (max-width:900px) and (min-width:701px){
    body:not(.lg-collapsed) #lgSidebar .lg-sidebar-logo img{max-width:52px;height:auto}
    body:not(.lg-collapsed) #lgSidebar .lg-nav-link{justify-content:center;padding:10px 0!important;width:100%;margin-right:0!important;transform:none!important}
    body:not(.lg-collapsed) #lgSidebar .lg-admin-label{display:none}
    body:not(.lg-collapsed) #lgSidebar .lg-sidebar-logout{width:44px;height:44px;margin:auto;padding:0}
    body:not(.lg-collapsed) #lgSidebar .lg-logout-label{display:none}
    body.lg-collapsed #lgSidebar .lg-admin-label,body.lg-collapsed #lgSidebar .lg-logout-label{display:inline}
    body.lg-collapsed #lgSidebar .lg-sidebar-logo img{max-width:100%;height:64px}
    body.lg-collapsed #lgSidebar .lg-nav-link{justify-content:flex-start;padding:10px 12px!important}
    body.lg-collapsed #lgSidebar .lg-sidebar-logout{width:100%;height:auto;padding:10px 12px}
}
@media(max-width:700px){
    body.lg-collapsed #lgSidebar .lg-admin-label,body.lg-collapsed #lgSidebar .lg-logout-label{display:inline}
    body.lg-collapsed #lgSidebar .lg-nav-link{justify-content:flex-start;padding:10px 12px!important}
    body.lg-collapsed #lgSidebar .lg-sidebar-logout{width:100%;height:auto;padding:10px 12px}
    body.lg-collapsed #lgSidebar .lg-sidebar-logo img{max-width:100%;height:64px}
}
@media(prefers-reduced-motion:reduce){#lgSidebar .lg-admin-menu-item,#lgSidebar .lg-admin-menu-item .lg-admin-icon-wrap,#lgSidebar .lg-admin-menu-item .lg-admin-label,#lgSidebar .lg-admin-logout-enter{opacity:1;animation:none!important;transform:none}#lgSidebar .lg-nav-link,#lgSidebar .lg-sidebar-logout{transition:none!important}}
</style>
<script>
document.addEventListener('DOMContentLoaded',function(){
    const form=document.getElementById('lgSidebarLogoutForm');
    if(!form)return;
    form.addEventListener('submit',function(){
        const button=form.querySelector('button[type="submit"]');
        if(!button)return;
        button.disabled=true;
        button.style.opacity='.7';
        const label=button.querySelector('.lg-logout-label');
        if(label)label.textContent='Logging Out...';
    });
});
</script>