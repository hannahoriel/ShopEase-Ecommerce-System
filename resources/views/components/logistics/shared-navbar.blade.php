@php
    $navbarUser = auth()->user();
    $navbarRole = ['logistics' => 'Logistics', 'sorting-center' => 'Sorting Center', 'delivery-hub' => 'Delivery Hub'][$navbarArea];
    $navbarPageKey = request()->segment(2) ?: 'dashboard';
    $navbarPageTitle = [
        'dashboard' => 'Dashboard', 'delivery-hub-dashboard' => 'Dashboard',
        'network-hubs' => 'Network Hubs', 'deliveries' => 'Network Hubs', 'delivery-hubs' => 'Delivery Hubs',
        'courier-registrations' => 'Courier Registrations', 'rider-registrations' => 'Rider Registrations',
        'scanners' => 'Scanners', 'parcel-tracking' => 'Parcel Tracking', 'parcel-disputes' => 'Parcel Disputes',
        'vehicles-drivers' => 'Vehicles and Drivers', 'vehicle-drivers' => 'Vehicles and Drivers',
        'logistics-earnings' => 'Logistics Earnings', 'earnings' => 'Logistics Earnings', 'finance' => 'Finance',
        'reports' => 'Reports', 'notifications' => 'Notifications', 'messages' => 'Messages',
        'account' => 'Account',
    ][$navbarPageKey] ?? \Illuminate\Support\Str::headline($navbarPageKey);
    $navbarEntity = match ($navbarArea) {
        'sorting-center' => \App\Models\Logistics\LogisticsBranch::where('user_id', $navbarUser->id)->first(),
        'delivery-hub' => \App\Models\Logistics\LogisticsDeliveryHub::where('user_id', $navbarUser->id)->first(),
        default => $navbarUser->logisticsProfile,
    };
    $navbarName = data_get($navbarEntity, 'business_name') ?: data_get($navbarEntity, 'hub_name') ?: data_get($navbarEntity, 'name') ?: $navbarUser->name ?: $navbarRole;
    $navbarPhoto = $navbarUser->profile_photo_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($navbarUser->profile_photo_path) : null;
    $navbarUnreadCount = $navbarUser->unreadNotifications()->count();
    $navbarNotifications = $navbarUser->unreadNotifications()->latest()->take(5)->get();
@endphp
<style>
.lg-navbar .lg-nav-left{min-width:0}.lg-navbar .lg-nav-left h2{overflow:hidden;text-overflow:ellipsis;max-width:40vw}
.lg-navbar .lg-profile-btn{gap:10px;padding:6px 12px 6px 10px;border:1px solid #e5e7eb;border-radius:10px;background:#fff;box-shadow:0 1px 3px #00000010;min-width:0;transition:box-shadow .2s}
.lg-navbar .lg-profile-btn:hover{box-shadow:0 4px 8px #00000014}.lg-navbar .lg-profile-copy{min-width:135px;text-align:left}
.lg-navbar .lg-profile-copy strong{font-size:16px;line-height:1.25;font-weight:600;color:#1f2937;max-width:235px}
.lg-navbar .lg-profile-copy small{font-size:12px;line-height:1.25;color:#9ca3af;display:block}
.lg-navbar .lg-profile-chevron{width:18px;height:18px;flex:0 0 18px;color:#374151;transition:transform .2s}
.lg-navbar .lg-profile-btn[aria-expanded="true"] .lg-profile-chevron{transform:rotate(180deg)}
.lg-navbar #lgProfileMenu{position:absolute;right:0;top:52px;width:225px;max-width:calc(100vw - 24px);padding:0;background:#fff;border:1px solid #f3f4f6;border-radius:16px;box-shadow:0 20px 25px -5px #0000001a,0 8px 10px -6px #0000001a;overflow:hidden;z-index:100}
.lg-navbar .lg-profile-menu-heading{padding:12px 16px;border-bottom:1px solid #f3f4f6}
.lg-navbar .lg-profile-menu-heading strong{display:block;font-size:14px;font-weight:600;color:#1f2937;overflow-wrap:anywhere;line-height:1.4}
.lg-navbar .lg-profile-menu-heading small{display:block;font-size:11px;color:#9ca3af;margin-top:4px}
.lg-navbar .lg-profile-menu-links,.lg-navbar .lg-profile-menu-footer{padding:6px}
.lg-navbar .lg-profile-menu-footer{border-top:1px solid #f3f4f6}
.lg-navbar #lgProfileMenu .lg-profile-menu-item{display:flex;align-items:center;gap:10px;width:100%;padding:10px;border:0;border-radius:12px;background:transparent;font:400 13px Poppins,Arial,sans-serif;color:#374151;text-align:left;text-decoration:none;transition:background .2s}
.lg-navbar #lgProfileMenu .lg-profile-menu-item:hover{background:#fff2ee}
.lg-navbar #lgProfileMenu .lg-profile-menu-logout{color:#dc2626}
.lg-navbar #lgProfileMenu .lg-profile-menu-logout:hover{background:#fef2f2}
.lg-navbar #lgProfileMenu [hidden]{display:none!important}
.lg-navbar .lg-notify-wrap{position:relative}.lg-navbar .lg-notify-button{position:relative}
.lg-navbar .lg-notify-count{position:absolute;top:-5px;right:-5px;border-radius:50%;min-width:18px;height:18px;padding:0 3px;background:#dc2626;color:#fff;font:600 10px/18px Poppins,Arial,sans-serif;text-align:center}
.lg-navbar .lg-notify-menu{position:absolute;right:0;top:40px;width:290px;max-width:calc(100vw - 24px);background:white;border-radius:16px;box-shadow:0 12px 28px #0002;border:1px solid #f3f4f6;overflow:hidden;z-index:100;color:#1f2937;font-family:Poppins,Arial,sans-serif}
.lg-navbar .lg-notify-menu[hidden]{display:none!important}.lg-navbar .lg-notify-heading{padding:12px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f3f4f6;font-size:12px}.lg-navbar .lg-notify-heading small{font-size:10px;color:#9ca3af}
.lg-navbar .lg-notify-list{max-height:320px;overflow:auto}.lg-navbar .lg-notify-item{display:block;padding:12px 16px;border-bottom:1px solid #f9fafb;text-decoration:none;color:inherit}.lg-navbar .lg-notify-item:hover{background:#fff5f1}
.lg-navbar .lg-notify-item strong{display:block;font-size:12px;font-weight:600}.lg-navbar .lg-notify-item p{font-size:11px;color:#6b7280;line-height:1.6;margin:4px 0;overflow-wrap:anywhere}.lg-navbar .lg-notify-item time{font-size:10px;color:#9ca3af}.lg-navbar .lg-notify-empty{padding:24px 16px;text-align:center;font-size:12px;color:#9ca3af}
.lg-navbar .lg-notify-all{display:block;padding:12px;text-align:center;color:#7b1b1b;font-size:12px;font-weight:600;text-decoration:none;border-top:1px solid #f3f4f6}
@media(max-width:700px){.lg-navbar .lg-navbar-caption{display:none}.lg-navbar .lg-profile-copy{min-width:0}.lg-navbar .lg-profile-copy strong{max-width:120px;font-size:13px}.lg-navbar .lg-profile-copy small{font-size:10px}.lg-navbar .lg-nav-left h2{max-width:28vw;font-size:15px}.lg-navbar .lg-nav-right{gap:12px}}
</style>
<header class="lg-navbar">
    <div class="lg-nav-left">
        <button type="button" id="lgSidebarToggle" class="lg-icon-button" aria-label="Toggle sidebar" aria-expanded="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
        <h2 title="{{ $navbarPageTitle }}">{{ $navbarPageTitle }}</h2>
    </div>
    <div class="lg-nav-right">
        <span class="lg-navbar-caption">{{ $navbarRole }} Management</span>
        <div class="lg-notify-wrap">
            <button type="button" id="lgNotifyButton" class="lg-icon-button lg-notify-button" title="Notifications" aria-label="Notifications" aria-controls="lgNotifyMenu" aria-expanded="false">
                <img src="{{ asset('icons/admin/dashboard/sidebar&navbar/notification.png') }}" alt="" width="24" height="28">
                @if($navbarUnreadCount > 0)<span class="lg-notify-count">{{ $navbarUnreadCount > 99 ? '99+' : $navbarUnreadCount }}</span>@endif
            </button>
            <div id="lgNotifyMenu" class="lg-notify-menu" hidden>
                <div class="lg-notify-heading"><strong>Notifications</strong><small>{{ $navbarUnreadCount ? $navbarUnreadCount.' new' : 'No new notifications' }}</small></div>
                <div class="lg-notify-list">
                    @forelse($navbarNotifications as $navbarNotification)
                        @php($navbarData = is_array($navbarNotification->data) ? $navbarNotification->data : [])
                        <a class="lg-notify-item" href="{{ route($navbarArea.'.notifications', ['filter' => 'unread']) }}">
                            <strong>{{ $navbarData['title'] ?? $navbarData['subject'] ?? 'ShopEase Notification' }}</strong>
                            <p>{{ $navbarData['message'] ?? $navbarData['body'] ?? $navbarData['description'] ?? 'You have a new update regarding your account.' }}</p>
                            <time>{{ $navbarNotification->created_at?->diffForHumans() ?? 'Date unavailable' }}</time>
                        </a>
                    @empty
                        <div class="lg-notify-empty">You have no unread notifications.</div>
                    @endforelse
                </div>
                <a class="lg-notify-all" href="{{ route($navbarArea.'.notifications') }}">View all notifications</a>
            </div>
        </div>
        <div class="lg-profile-wrap">
            <button class="lg-profile-btn" id="lgProfileButton" type="button" aria-expanded="false" aria-controls="lgProfileMenu">
                <x-account-avatar :name="$navbarName" :image="$navbarPhoto" size="36px" />
                <span class="lg-profile-copy"><strong title="{{ $navbarName }}">{{ $navbarName }}</strong><small>{{ $navbarRole }} Account</small></span>
                <svg class="lg-profile-chevron" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="lg-profile-menu" id="lgProfileMenu" hidden>
                <div class="lg-profile-menu-heading"><strong>{{ $navbarName }}</strong><small>{{ $navbarRole }} Account</small></div>
                <div class="lg-profile-menu-links">
                    <a class="lg-profile-menu-item" href="{{ route($navbarArea.'.account') }}"><span>My Account</span></a>
                    <a class="lg-profile-menu-item" href="{{ route($navbarArea.'.account') }}#privacy"><span>Settings</span></a>
                </div>
                <div class="lg-profile-menu-footer">
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="lg-profile-menu-item lg-profile-menu-logout" type="submit"><span>Log Out</span></button></form>
                </div>
                {{-- Retain hooks used by older page scripts; photo editing is in My Account. --}}
                <button type="button" id="lgChangeProfilePhoto" hidden>Change Profile Photo</button>
                <input id="lgProfilePhotoInput" type="file" accept="image/jpeg,image/png,image/webp" hidden>
            </div>
        </div>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const byId=id=>document.getElementById(id), profile=byId('lgProfileButton'), menu=byId('lgProfileMenu'), bell=byId('lgNotifyButton'), inbox=byId('lgNotifyMenu');
 const close=()=>{menu.hidden=true;inbox.hidden=true;profile.setAttribute('aria-expanded','false');bell.setAttribute('aria-expanded','false');};
 // Capture prevents older page-specific alert/profile handlers from also firing.
 document.addEventListener('click',e=>{
   const button=e.target.closest('#lgNotifyButton,#lgProfileButton,#lgSidebarToggle,#lgChangeProfilePhoto');
   if(!button){if(!e.target.closest('.lg-profile-wrap,.lg-notify-wrap'))close();return;}
   e.stopImmediatePropagation();
   if(button.id==='lgSidebarToggle'){
     if(window.parent!==window&&window.parent.ShopEaseNavigation){window.parent.ShopEaseNavigation.toggleSidebar();return;}
     const collapsed=document.body.classList.toggle('lg-collapsed');button.setAttribute('aria-expanded',String(!collapsed));return;
   }
   if(button.id==='lgChangeProfilePhoto'){byId('lgProfilePhotoInput').click();return;}
   const notification=button===bell, target=notification?inbox:menu, open=target.hidden;
   close();target.hidden=!open;button.setAttribute('aria-expanded',String(open));
 },true);
 document.addEventListener('keydown',e=>{if(e.key==='Escape')close();});
 byId('lgProfilePhotoInput').addEventListener('change',async e=>{
   e.stopImmediatePropagation();
   const file=e.target.files?.[0];if(!file)return;
   if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>5*1024*1024){e.target.value='';window.alert('Choose a JPG, PNG, or WebP image under 5 MB.');return;}
   const form=new FormData();form.append('profile_photo',file);
   try{
     const response=await fetch('/api/v1/profile/photo',{method:'PUT',credentials:'same-origin',headers:{Accept:'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''},body:form});
     const result=await response.json();if(!response.ok)throw Error(result.message||'Unable to update profile photo.');
     const avatar=profile.querySelector('.account-avatar');
     if(avatar&&result.profile_photo_url){let img=avatar.querySelector('img');if(!img){img=document.createElement('img');avatar.append(img);}img.src=result.profile_photo_url;img.style.cssText='position:absolute;inset:0;width:100%;height:100%;border-radius:inherit;object-fit:cover;';}
   }catch(error){window.alert(error.message||'Unable to update profile photo.');}finally{e.target.value='';}
 },true);
});
</script>
